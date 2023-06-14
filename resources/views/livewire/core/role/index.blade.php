<div>
    
    <x-page
        :breadcrumbs="$breadcrumbs"
    >  
       
        <x-slot:title>Roles</x-slot>

        <x-slot:description>
            {{ !$addRole ? 'Manage roles here' : 'Create a new Role here' }}
        </x-slot>

        <x-slot:content>
            @if($addRole)
                @include('livewire.core.role.partials.form', ['button_text' => 'Add Role'])
            @else
                <div class="row px-2">
                    <div class="card border-light shadow-sm mb-4" style="min-height: 600px">
                        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                            @can('roles.manage')
                                <button wire:click="create()" class="btn btn-primary btn-sm float-end" type="button"><i class="fa fa-plus" aria-hidden="true"></i> Add Role</button>
                            @endcan

                            <h3 class="h5 mb-0">Role List</h3>
                        </div>

                        <div class="card-body">
                            <livewire:core.role.table />
                        </div>
                    </div>
                </div>

            @endif
        </x-slot>
            
    </x-page>
    
</div>




