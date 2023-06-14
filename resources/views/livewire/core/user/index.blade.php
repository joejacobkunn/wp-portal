<div>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
        
        <div class="d-block mb-4 mb-md-0 mt-2">

            <x-breadcrumbs
                :breadcrumbs="$breadcrumbs"
            />

            <h2 class="h4">Users</h2>
            
            @if($addRecord)
                <p class="mb-0">Create a new User here</p>
            @else
                <p class="mb-0">Manage users here</p>
            @endif
            
        </div>

    </div>

    @include('partials.flash')

    @if($addRecord)
        @include('livewire.core.user.partials.form', ['button_text' => 'Add User'])
    @else
        @include('livewire.core.user.partials.listing')
    @endif

</div>
