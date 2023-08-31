<div>

    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Users Authentication Log</x-slot>

            <x-slot:description>
                See login and logout details
                </x-slot>

                <x-slot:content>
                    <div class="card border-light shadow-sm mb-4" style="min-height: 600px">
                        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">

                            <h3 class="h5 mb-0">
                                Authentication Logs
                            </h3>
                        </div>


                        <div class="card-body">
                            <livewire:core.user.authentication-table />
                        </div>
                    </div>


                    </x-slot>

    </x-page>

</div>