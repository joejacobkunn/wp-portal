<div>
    @if(!empty($breadcrumbs))
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            @php
                if(!empty($showRootBreadcrumb) && !empty($rootBreadcrumb))  {
                    array_unshift($breadcrumbs, $rootBreadcrumb);
                }
            @endphp

            @foreach ($breadcrumbs as $link)
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

