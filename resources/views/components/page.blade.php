<div class="x-page">

    @if(!empty($sidebar))
        @section('sidebar')
            {{ $sidebar }}
        @endsection
    @endif

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-first">
                    @if(!empty($title))
                    <h3 class="x-page-title">
                        {{ $title }}
                    </h3>
                    @endif

                    @if(!empty($description))
                    <p class="text-subtitle text-muted">
                        {{ $description }}
                    </p>
                    @endif
                </div>

                    <div class="col-12 col-md-6 order-md-2 order-last">
                        @if(!empty($breadcrumbs))
                            <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
                        @endif
                    </div>
            </div>
        </div>

        <section id="basic-horizontal-layouts">
            <div class="row match-height">
                <div class="col-md-12 col-12">

                    <div class="x-page-content">
                        @if(!empty($content))
                        {{ $content }}
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>

</div>

