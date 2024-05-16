<div class="row">
    <div class="col-12 col-md-12">
        <div class="card card-body shadow-sm mb-4">
            <form wire:submit.prevent="submit">

                @if (isset($user))
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="code">User ID</label>
                            <p class="small pe-4">{{ $user->id }}</p>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-forms.input label="Name" model="user.name" lazy />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-forms.input label="Email" model="user.email" lazy :disabled="$user->id" />
                    </div>
                </div>

                @can('manageRole', auth()->user())
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <x-forms.select label="Role" model="selectedRole" :options="$roles" :selected="$selectedRole ?? null"
                                label-index="label" value-index="name" />
                        </div>

                        <div class="col-md-6 mb-3">
                            <x-forms.input label="Demo Equipment ID" model="user.unavailable_equipments_id"
                                hint="Input reasunavty value, leave blank if n/a" lazy />
                        </div>

                    </div>
                @endcan

                <div class="mt-2">
                    <button type="submit" class="btn btn-success">
                        <div wire:loading wire:target="{{ isset($user) ? 'save' : 'submit' }}">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>

                        {{ $button_text }}

                    </button>
                    <button type="button" wire:click="cancel" class="btn btn-gray-200">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
