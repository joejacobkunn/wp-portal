<div class="row">
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-center align-items-center flex-column">
                    <div class="avatar avatar-2xl">
                        @php
                            $mediaUrl = $user->getFirstMediaUrl($user::DOCUMENT_COLLECTION);
                        @endphp
                        @if ($mediaUrl)
                        <img class="scheduler-driver-img-thumbnail" src="{{ $mediaUrl }}"
                                 alt="Avatar">
                        @else
                            <p class="small">No image available</p>
                        @endif
                    </div>

                    <h3 class="mt-3">{{ $user->name }}</h3>
                    <p class="text-small">{{$user->title}}</p>
                </div>
            </div>
        </div>
        </div>
    <div class="col-12 col-lg-8 col-md-8 col-xxl-8">
        <div class="card border-light shadow-sm mb-4">
            <div class="card-header border-gray-300 p-3 mb-4 mb-md-0" :key="'drivers'.time()">
                <livewire:component.action-button :actionButtons="$actionButtons" :key="'staff' . time()">
                    <h3 class="h5 mb-0"><i class="fas fa-bars me-1"></i> Overview</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">

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
