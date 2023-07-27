<div>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
        
        <div class="page-title-div d-block mb-4 mb-md-0 mt-3">
            
            <livewire:component.breadcrumb
                :breadcrumbs="$breadcrumbs"
                key="{{now()}}"
            >

            <h2 class="h4">Role Info</h2>
            
        </div>

    </div>

    @include('partials.flash') 

    @if($editRole)
         @include('livewire.core.role.partials.form', ['role' => $role, 'button_text' => 'Update Role'])
    @else
        @include('livewire.core.role.partials.view', ['role' => $role])
    @endif
    
</div>

