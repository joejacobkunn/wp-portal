<div class="x-page">

    @if(!empty($sidebar))
        @section('sidebar')
            {{ $sidebar }}
        @endsection
    @endif

    <div class="x-page-title d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
        <div class="page-title-div d-block mb-4 mb-md-0 mt-3">

            @if(!empty($breadcrumbs))
                <livewire:component.breadcrumb
                    :breadcrumbs="$breadcrumbs"
                    :route-params="request()->route()->parameters()"
                    key="{{ 'bc-'. now() }}">
            @endif

            @if(!empty($title))
                <h2 class="h4">{{ $title }}</h2>
            @endif

            @if(!empty($description))
                <p class="mb-0">{{ $description }}</p>
            @endif

        </div>

    </div>

    <div class="x-page-content">
        @if(!empty($content))
            {{ $content }}
        @endif
    </div>
</div>