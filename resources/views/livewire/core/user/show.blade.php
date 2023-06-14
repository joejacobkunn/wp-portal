<div>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
        
        <div class="d-block mb-4 mb-md-0 mt-2">

            <x-breadcrumbs
                :breadcrumbs="$breadcrumbs"
            />

            <h2 class="h4">Users #{{ $user->id }}</h2>
            
            @if($editRecord)
                <p class="mb-0">Edit User here</p>
            @else
                <p class="mb-0">View User here</p>
            @endif
            
        </div>

    </div>

    @include('partials.flash') 

    @if($editRecord)
        @include('livewire.core.user.partials.form', ['button_text' => 'Update User'])
    @else
        @include('livewire.core.user.partials.view')
    @endif
</div>

