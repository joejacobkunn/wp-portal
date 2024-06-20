

    <div class="card-body">
        @if ($this->configured)

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="home-tab"
                        href="{{ route('equipment.warranty.index') }}" role="tab"
                        aria-controls="home" aria-selected="true">Brand Configurator</a>
                </li>

            </ul>

            <div class="tab-content mt-4" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel"
                    aria-labelledby="home-tab">

                    <livewire:equipment.warranty.brand-configurator.warranty-table lazy>

                </div>
            </div>
        @else
            <div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Your account
                has
                not been configured in the Portal to use
                this app. Contact Mark Meister to resolve this</div>
        @endif
    </div>

