<div>

    <x-page
        :breadcrumbs="$breadcrumbs"
    >

        <x-slot:title>Dashboard</x-slot>

        <x-slot:description>

        </x-slot>

        <x-slot:sidebar>
        </x-slot>

        <x-slot:content>
            <div class="card border-light shadow-sm mb-4">
                <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                    <h4 class="card-title"></h4>
                </div>
                <div class="card-content">
                    <div class="card-body">

                    </div>
                </div>
            </div>
            <livewire:component.sms.sms-component :phone="'+13137188690'" :email="'arun@gmail.com'" lazy>
        </x-slot>

    </x-page>

</div>
