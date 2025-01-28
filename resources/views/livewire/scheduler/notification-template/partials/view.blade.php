<div class="row px-2">
    <div class="card border-light shadow-sm mb-4">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
            <livewire:component.action-button :actionButtons="$actionButtons">
                <h3 class="h5 mb-0">Overview</h3>
        </div>

        <div class="card-body">

            <div class="alert alert-light-info color-info" role="alert">
                <i class="fas fa-info-circle"></i> {{ $template->description }}
            </div>

            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Name</h3>
                        <p class="small pe-4">{{ $template->name ?? '-' }}</p>
                    </div>
                    <div>
                </li>


                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Email Subject</h3>
                        <p class="small pe-4">{!! $template->email_subject ?? '-' !!}</p>
                    </div>
                    <div>
                </li>

                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Email Content</h3>
                        <p class="small pe-4">{!! $template->email_content ?? '-' !!}</p>
                    </div>
                    <div>
                </li>

                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">SMS Content</h3>
                        <p class="small pe-4">{{ $template->sms_content ?? '-' }}</p>
                    </div>
                    <div>
                </li>

            </ul>
        </div>
    </div>
</div>
