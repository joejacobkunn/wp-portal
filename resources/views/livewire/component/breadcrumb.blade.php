<div class="x-breadcrumb">
    @if(!empty($breadcrumbs))
    <nav aria-label="breadcrumb" class="breadcrumb-header float-end float-lg-end">
        <ol class="breadcrumb breadcrumb-dark">
            @php
                if(!empty($showRootBreadcrumb) && !empty($rootBreadcrumb))  {
                    array_unshift($breadcrumbs, $rootBreadcrumb);
                }
            @endphp

            @foreach ($breadcrumbs as $link)
                @php
                    if(isset($link['route_name'])) {
                        $link['href'] = route($link['route_name']);
                    }
                @endphp
                <li class="breadcrumb-item">
                    @if(isset($link['href']))
                        <a href="{{ $link['href'] }}">
                    @endif

                    @if(isset($link['icon']))
                        <span class="fas {{ $link['icon'] }}"></span>
                    @endif

                    {{ $link['title'] ?? "" }}

                    @if(isset($link['href']))
                    </a>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
    @endif
</div>
