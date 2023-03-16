<div>
    <div class="card border-light shadow-sm mb-4">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0" :key="'bew'.time()">
            <h3 class="h5 mb-0"><i class="fas fa-bars me-1"></i> Modules</h3>
        </div>

        <div class="card card-body border-0 mb-4 mb-xl-0 mt-2">

            <div class="alert alert-light-info color-info">
                Enable or disable modules for {{$account->name}} here
            </div>

            <ul class="list-group list-group-flush">
                @foreach($modules as $module)
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">{{$module->name}}</h3>
                            <p class="small pe-4">{{$module->description}}</p>
                        </div>

                        @role('master-admin')
                            <div class="btn-group btn-group-sm" role="group" aria-label="...">
                                <button wire:click="toggleModule({{$module->id}},1)" type="button" class="@if($account->modules->contains($module->id)) btn btn-success @else btn btn-outline-success @endif"><i class="bi bi-check-circle"></i></button>
                                <button wire:click="toggleModule({{$module->id}},0)" type="button" class="@if($account->modules->contains($module->id)) btn btn-outline-danger @else btn btn-danger @endif"><i class="bi bi-x-circle"></i></button>
                            </div>
                        @endrole

                    </li>
                @endforeach
            </ul>
        </div>


    </div>
</div>
