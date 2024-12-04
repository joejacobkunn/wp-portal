<div class="row service-area">
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
                                        <div class="" >
                                            <x-forms.checkbox
                                                :label="ucfirst($day)"
                                                :name="'days.' . $day . '.enabled'"
                                                :value="true"
                                                :model="'days.' . $day . '.enabled'"
                                            />
                                        </div>
                                        <div  class="ms-1">
                                                <select model="days.{{ $day }}.schedule" class="form-select form-select-sm border-secondary-subtle days-select"
                                                @if(!$values['enabled']) disabled @endif>

                                                    <option value="">Select </option>
                                                    @foreach ($scheduleOptions as $key => $option)
                                                        <option value="{{$key}}">{{$option}}</option>
                                                    @endforeach
                                                </select>
                                                @error('days.' . $day . '.schedule')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror

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
