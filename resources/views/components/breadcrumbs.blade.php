<div>
    <livewire:component.breadcrumb
        :breadcrumbs="$breadcrumbs"
        :route-params="request()->route()->parameters()"
        key="bc-{{ $key ?? now() }}"
    >
</div>