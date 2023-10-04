<?php

namespace App\Http\Livewire\Reporting;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Report;
use App\Models\Reporting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Rappasoft\LaravelLivewireTables\Views\Column;
use PHPSQLParser\PHPSQLParser;

class ReportingTable extends DataTableComponent
{
    use AuthorizesRequests;

    public $query;

    private $colors = ['active', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];

    public $groupby;

    public function configure(): void
    {
        $this->setPrimaryKey('id');

        $this->setPerPageAccepted([50, 75, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);

        $this->setTrAttributes(function($row, $index) {
            if(!empty($this->groupby))
            {
                if (!array_key_exists($row->{$this->groupby}, $this->colors)) {
                    $this->colors[$row->{$this->groupby}] = $this->colors[array_rand($this->colors,1)];
                }

                return [
                    'class' => 'table-'.$this->colors[$row->{$this->groupby}],
                ];
      
            }
       
            return [];
        });

    }

    public function boot(): void
    {

    }

    public function columns(): array
    {
        $columns = [];
        $parser = new PHPSQLParser($this->query, true);
        $parsed_columns = $parser->parsed;

        foreach($parsed_columns['SELECT'] as $column){
            if(is_array($column['alias'])) $column_name = $this->clean($column['alias']['name']);
            else $column_name = $this->clean(end($column['no_quotes']['parts']));
            $columns[] =  Column::make($column_name, $column_name)->sortable()->searchable();
        }

        return $columns;

    }

    public function filters(): array
    {
        return [
            
        ];
    }

    public function builder(): Builder
    {
        return Reporting::setQuery($this->query);
    }

    private function clean($string)
    {
        $string = str_replace('"','',$string);
        $string = str_replace("'",'',$string);
        return $string;
    }
}
