<?php

namespace App\Http\Livewire\Core\User;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Core\User\Traits\FormRequest;
use App\Models\Core\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Show extends Component
{
    use AuthorizesRequests, FormRequest;

    public User $user;

    public $editRecord = false;

    public $actionButtons = [];

    protected $listeners = [
        'deleteRecord' => 'delete',
        'edit' => 'edit',
        'updateStatus' => 'updateStatus',
        'closeModal' => 'closeModal'
    ];

    public function breadcrumbs()
    {
        return [
            [
                'title' => 'Users',
                'href' => route('core.user.index'),
            ],
            [
                'title' => $this->user->name,
            ],
        ];
    }

    public function mount(User $user)
    {
        $this->authorize('view', $user);
        $this->formInit();
        $this->breadcrumbs = $this->breadcrumbs();

        if (auth()->user()->can('users.manage')) {
            $this->actionButtons = [
                [
                    'icon' => 'fa-edit',
                    'color' => 'primary',
                    'listener' => 'edit',
                ],
                [
                    'icon' => 'fa-trash',
                    'color' => 'danger',
                    'confirm' => true,
                    'confirm_header' => 'Confirm Delete',
                    'listener' => 'deleteRecord',
                ],
            ];
        }
    }

    public function render()
    {
        return view('livewire.core.user.show')->extends('livewire-app');
    }

    // @TODO Remove after confirmation on WP-8 Remove User info Edit
    public function edit()
    {
        $this->authorize('update', $this->user);
        $this->editRecord = true;
    }

    /**
     * Delete existing user
     */
    public function delete()
    {
        $this->authorize('delete', $this->user);

        $this->user->update([
            'email' => $this->user->email . '+del+'. time()
        ]);

        $this->user->delete();
        session()->flash('success', 'User deleted !');

        return redirect()->route('core.user.index');
    }

    public function cancel()
    {
        // @TODO Remove after confirmation on WP-8 Remove User info Edit
        $this->editRecord = false;
    }
}
