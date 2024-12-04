<div class="row">
    <div class="col-12 col-md-12">
        <form wire:submit.prevent="submit()">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <x-forms.input label="Zone Name" model="name" lazy />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group x-input">
                            <div class="form-group">
                                <x-forms.textarea label="Description" model="description" rows="5" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group x-input">
                            <div class="form-group">
                                <label class="form-label">Schedule Days</label>
                                @foreach($days as $day => $values)
                                @php
                                   $enabled =  $values['enabled'];
                                @endphp
                                    <div class="d-flex align-items-center mb-2 border rounded p-2">
                                        <div class="me-3" >
                                            <x-forms.checkbox
                                                :label="ucfirst($day)"
                                                :name="'days.' . $day . '.enabled'"
                                                :value="true"
                                                :model="'days.' . $day . '.enabled'"
                                            />
                                        </div>
                                        <div class="ms-auto" >
                                            <x-forms.select
                                                :label="''"
                                                :model="'days.' . $day . '.schedule'"
                                                :options="$scheduleOptions"
                                                :defaultOption="true"
                                                :hasAssociativeIndex="true"
                                                :disabled="!$enabled"
                                                :key="'days.' . $day . '.schedule'.$days[$day]['enabled']"
                                            />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            <hr>
            <di v class="mt-2 float-start">

                <button type="submit" class="btn btn-primary">
                    <div wire:loading wire:target="submit">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    {{ $button_text }}
                </button>

                <button type="button" wire:click="cancel" class="btn btn-light-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>
