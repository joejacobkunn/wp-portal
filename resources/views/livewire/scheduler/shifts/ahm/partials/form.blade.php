<div class="row service-area">
    <div class="col-12 col-md-12">
        <form wire:submit.prevent="submit()">
            <div class="row">
                <div class="col-md-12 mb-3">
                        @php
                            // List of all months
                            $monthList = [
                                'january', 'february', 'march', 'april', 'may', 'june',
                                'july', 'august', 'september', 'october', 'november', 'december'
                            ];
                            $daysList = [
                                'monday', 'tuesday', 'wednesday', 'thrusday', 'friday', 'saturday',
                                'sunday'
                            ];
                        @endphp

                        <div class="row columnRow">
                            @error('shiftData')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                            @enderror
                            <div class="col-md-12">
                                @foreach ($monthList as $monthItem)

                                <div class="accordion">
                                    <div class="accordion-item mb-2">
                                        <h2 class="accordion-header" id="headingtemplate">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse-template-{{$monthItem}}" aria-expanded="true"
                                                aria-controls="collapse-template-{{$monthItem}}">
                                                {{ucfirst($monthItem)}}
                                            </button>
                                        </h2>
                                        <div id="collapse-template-{{$monthItem}}" class="accordion-collapse collapse show"
                                            aria-labelledby="headingtemplate">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    @foreach ($daysList as $day)
                                                        <div class="col-3 mb-3">
                                                            <x-forms.checkbox :label="ucfirst($day)" :name="'months.' .$monthItem. '.days.' .$day. '.status'" :value="true"
                                                                :model="'months.' .$monthItem. '.days.' .$day. '.status'" />
                                                        </div>
                                                    @endforeach
                                                    <hr>
                                                </div>
                                                <div class="row">
                                                    @if (isset($months[$monthItem]))
                                                    @foreach ($months[$monthItem] as $key => $month)

                                                        @foreach ($month as $j =>  $day)
                                                            <div class="border p-3 rounded mb-2">
                                                                <h5 class="mb-3">{{ ucfirst($monthItem).' - '. ucfirst($j).' Shifts'}}</h5>

                                                                <div class="row">
                                                                    @if(isset($shiftData[$monthItem][$j]))
                                                                    @foreach ($shiftData[$monthItem][$j] as $m => $shifts)
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Shift</label>
                                                                            <select class="form-control" wire:model="{{'shiftData.' .$monthItem .'.'.$j. '.' . $m.'.shift'}}">
                                                                                <option value="">Select Shift</option>
                                                                                @foreach ($shiftList as $option)

                                                                                 <option value="{{$option}}" @if(isset($shiftData[$monthItem][$j]['shift']))@selected($shiftData[$monthItem][$j][$m]['shift'] == $option) @endif>{{$option}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            @error('shiftData.' .$monthItem .'.'.$j. '.' . $m.'.shift')
                                                                                <span class="text-danger">{{$message}}</span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <x-forms.input type="number"
                                                                        label="Slot"
                                                                        model="{{'shiftData.' .$monthItem .'.'.$j. '.' . $m.'.slots'}}"
                                                                        lazy />
                                                                    </div>
                                                                    @endforeach
                                                                    @elseif(isset($months[$monthItem]['days'][$j]))

                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Shift</label>
                                                                            <select class="form-control" wire:model="{{'shiftData.' .$monthItem .'.'.$j. '.0.shift'}}">
                                                                                <option value="">Select Shift</option>
                                                                                @foreach ($shiftList as $option)

                                                                                    <option value="{{$option}}" @if(isset($shiftData[$monthItem][$j])) @selected($shiftData[$monthItem][$j][0]['shift'] == $option) @endif>{{$option}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <x-forms.input type="number"
                                                                        label="Slot"
                                                                        model="{{'shiftData.' .$monthItem .'.'.$j. '.0.slots'}}"
                                                                        lazy />
                                                                    </div>
                                                                    @else
                                                                    @endif
                                                                    <div class="col-12">
                                                                        <button type="button" wire:click="addShift('{{$monthItem}}', '{{$j}}')" class="btn btn-light-success">Add More Shift</button>
                                                                        <button type="button" wire:click="RemoveShift('{{$monthItem}}', '{{$j}}')" class="btn btn-light-danger">Remove Last Shift</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                </div>
            </div>
            <hr>
            <div class="mt-2 float-start">
                <button type="submit" class="btn btn-success">
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
