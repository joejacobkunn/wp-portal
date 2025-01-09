<div class="row">
    <div class="col-8 col-md-12 col-xxl-12">
        <div class="card border-light shadow-sm mb-4">
            <div class="card-header border-gray-300 p-3 mb-4 mb-md-0" :key="'drivers'.time()">
                <livewire:component.action-button :actionButtons="$actionButtons" :key="'staff' . time()">
                    <h3 class="h5 mb-0"><i class="fas fa-bars me-1"></i> Overview</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div class="d-flex align-items-center w-100">
                            <div class="flex-grow-1">
                                <h3 class="h6 mb-1">Name</h3>
                                <p class="small pe-4">{{ $user->name }}</p>                            </div>

                            <div>
                                @if ($user->getFirstMediaUrl(\App\Models\Core\User::DOCUMENT_COLLECTION))
                                <img src="{{ $user->getFirstMediaUrl(\App\Models\Core\User::DOCUMENT_COLLECTION) }}"
                                         alt="User Image"
                                         class="img-fluid rounded img-thumbnail scheduler-driver-img-thumbnail ">
                                @else
                                    <p class="small">No image available</p>
                                @endif
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Title</h3>
                            <p class="small pe-4">{{ $user->title }}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Email</h3>
                            <p class="small pe-4">{{ $user->email }}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div class="w-100">
                            <h3 class="text-sm font-semibold mb-3">Skills</h3>
                            @if ($user->skills)
                            <ul>
                                @foreach (explode(",", $user->skills?->skills) as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                    </li>

                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                        <div>
                            <h3 class="h6 mb-1">Last Updated At</h3>
                            <p class="small pe-4">
                                {{ $user->updated_at?->format(config('app.default_datetime_format')) }}</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>


    </div>

</div>
