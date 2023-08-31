<?php

namespace App\Http\Livewire\Core\User;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Core\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog as Log;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Jenssegers\Agent\Agent;

class AuthenticationTable extends DataTableComponent
{
    use AuthorizesRequests;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('login_at', 'desc');
        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
    }

    public function boot(): void
    {

    }

    public function columns(): array
    {
        return [
            Column::make('User', 'authenticatable_id')
            ->format(function($value) {
                return User::find($value)->name;
            })
            ->searchable(function (Builder $query, $searchTerm) {
                $query->whereHasMorph('authenticatable', '*', function ($query) use($searchTerm) {
                    $query->where('name', 'like', '%'.$searchTerm.'%');
                });
            }),
            Column::make('IP Address', 'ip_address')
                ->searchable(),
            Column::make('Browser', 'user_agent')
                ->searchable()
                ->format(function($value) {
                    $agent = tap(new Agent, fn($agent) => $agent->setUserAgent($value));
                    return $agent->platform() . ' - ' . $agent->browser();
                }),
            Column::make('Location')
                ->searchable(function (Builder $query, $searchTerm) {
                    $query->orWhere('location->city', 'like', '%'.$searchTerm.'%')
                        ->orWhere('location->state', 'like', '%'.$searchTerm.'%')
                        ->orWhere('location->state_name', 'like', '%'.$searchTerm.'%')
                        ->orWhere('location->postal_code', 'like', '%'.$searchTerm.'%');
                })
                ->format(fn ($value) => $value && $value['default'] === false ? $value['city'] . ', ' . $value['state'] : '-'),
            Column::make('Login At')
                ->sortable()
                ->format(fn($value) => $value ? Carbon::parse($value)->timezone('America/Detroit')->toDayDateTimeString() : '-'),
            Column::make('Login Successful')
                ->sortable()
                ->format(fn($value) => $value === true ? 'Yes' : 'No'),
            Column::make('Logout At')
                ->sortable()
                ->format(fn($value) => $value ? Carbon::parse($value)->timezone('America/Detroit')->toDayDateTimeString() : '-'),
            Column::make('Cleared By User')
                ->sortable()
                ->format(fn($value) => $value === true ? 'Yes' : 'No'),
        ];
    }

    public function filters(): array
    {
        return [
            
        ];
    }

    public function builder(): Builder
    {
        return Log::query()
        ->where('authenticatable_type', User::class);
        
    }
}
