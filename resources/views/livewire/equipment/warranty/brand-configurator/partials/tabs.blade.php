<ul class="nav nav-tabs" id="myTab" role="tablist" style="margin-bottom: 20px">
    @can('equipment.warranty.view')
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="home-tab"
            href="{{ route('equipment.warranty.index') }}" role="tab"
            aria-controls="home" aria-selected="true">Brand Configurator</a>
    </li>
    @endcan
</ul>
