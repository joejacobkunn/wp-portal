<div class="row">
    <div class="col-12 col-md-12">
        <div class="card card-body shadow-sm mb-4">
            <form wire:submit.prevent="{{ (!empty($role->id) ? 'save()' : 'submit()')}}">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-forms.input
                            label="Name"
                            model="role.label"
                            lazy
                        />
                    </div>

                    <div class="col-md-6 mb-3">
                        <x-forms.select
                            label="Type"
                            model="selectedType"
                            :options="$roleTypes"
                            :selected="$selectedType ?? null"
                            :listener="'selectedType:changed'"
                            label-index="label"
                            value-index="name"
                            key="{{ 'selectedType_'.($selectedType) }}"

                            />
                    </div>
                </div>

                <div role="separator" class="dropdown-divider my-3"></div>

                <h3 class="h5 mb-3">Permissions</h3>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="accordion permission-accordion">
                            @foreach ($permissionGroups as $group => $permissionGroup)
                            <div class="accordion-item mb-2">
                                <h2 class="accordion-header" id="heading{{ $group }}">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $group }}" aria-expanded="true" aria-controls="collapse-{{ $group }}">
                                    {{ $permissionGroup["group_name"] }}
                                </button>
                                </h2>
                                <div id="collapse-{{ $group }}" class="accordion-collapse collapse show" aria-labelledby="heading{{ $group }}">
                                <div class="accordion-body">
                                    <x-forms.checkbox-group
                                        name="selectedPermissions[]"
                                        :items="$permissionGroup['permissions']"
                                        model="selectedPermissions"
                                        label-index="label"
                                        value-index="name"
                                        hint-index="description"
                                    />
                                </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-2">
                    <button type="submit" class="btn btn-success">

                        <div wire:loading wire:target="{{ (!empty($role->id) ? 'save' : 'submit')}}">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>

                        {{$button_text}}

                    </button>
                    <button type="button" wire:click="cancel" class="btn btn-gray-200">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
