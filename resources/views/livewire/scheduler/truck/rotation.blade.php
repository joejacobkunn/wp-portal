<div>
    <div class="row">
        <div class="col-8 col-md-12 col-xxl-12">
            <div class="card border-light shadow-sm mb-4">
                <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                    <h3 class="h5 mb-0">
                        <i class="fas fa-retweet me-1"></i> Rotations

                        @if (!$editRotation && count($rotations))
                            <button type="button" wire:click="editRotationAction" class="btn btn-sm float-end">
                                <i class="fa fa-edit" aria-hidden="true"></i> Edit
                            </button>
                        @endif
                    </h3>


                    <hr class="mb-0" />
                </div>
                <div class="card-body">

                    <div class="alert alert-light-warning color-warning"><i class="bi bi-exclamation-triangle"></i>
                        Please configure <strong>Shifts</strong> first for each schedule type for rotations to work.
                    </div>

                    @if (!$editRotation && !count($rotations))
                        <div class="mt-">
                            <p>No rotations are currently defined. <span>Click the link below to add a new
                                    rotation.</span></p>
                            <button type="button" wire:click="editRotationAction" class="btn btn-outline-success mb-2">
                                <span wire:loading wire:target="editRotationAction"
                                    class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <i class="fa fa-plus"></i> Add New Rotation
                            </button>
                        </div>
                    @elseif(!$editRotation && count($rotations))
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="h6 mb-1">Service Type</h3>
                                <p class="small pe-4">{{ $truck->service_type }}</p>
                            </div>
                            <div class="col-md-6">
                                <h3 class="h6 mb-1">Baseline Date</h3>
                                <p class="small pe-4">{{ $truck->baseline_date }}</p>
                            </div>
                        </div>

                        <h3 class="h6 mb-1">Selected Zones</h3>
                        <ul>
                            @foreach ($rotations as $rotation)
                                <li class="mt-2"><strong>{{ $rotation['zone']['name'] ?? '' }}</strong></li>
                            @endforeach
                        </ul>
                    @else
                        <div>
                            <div class="row mb-4">
                                <div class="col-sm-6">
                                    <x-forms.select label="Service Type" model="serviceType" :selected="$serviceType"
                                        :options="$serviceTypes" />
                                </div>
                                <div class="col-sm-6">
                                    <x-forms.datepicker label="Baseline Date" type="date" model="baselineDate"
                                        :value="$baselineDate" />
                                </div>
                            </div>

                            <hr />

                            <div class="rotation-form-div">
                                @foreach ($rotationData as $index => $rotaionRecord)
                                    <div class="rotation-ind-div" data-index="{{ $index }}">
                                        <span class="drag-icon me-3 mt-2">
                                            <i class="fas fa-grip-vertical"></i>
                                        </span>
                                        <div class="select-div">
                                            <x-forms.select model="rotationData.{{ $index }}" :options="$zones"
                                                :selected="$rotationData[$index] ?? ''" has-associative-index :key="$index . 'rotation' . time()" />
                                        </div>

                                        @if (count($rotationData) > 1 || !$loop->first)
                                            <button wire:click="removeRotationItem('{{ $index }}')"
                                                wire:confirm-action data-confirm-type="danger"
                                                data-confirm-title="Confirm"
                                                class="btn btn-danger btn-sm float-end ms-3">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" wire:click="addRotationItem" class="btn btn-outline-primary my-2">
                                <span wire:loading wire:target="addRotationItem"
                                    class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

                                <i class="fa fa-plus"></i> Add More
                            </button>

                            <hr />

                            <button type="button" wire:click="saveRotationData" class="btn btn-success my-2">
                                <span wire:loading wire:target="saveRotationData"
                                    class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

                                <i class="fa fa-plus"></i> Save Changes
                            </button>
                            <button type="button" wire:click="cancelRotationData" class="btn btn-outline-primary my-2">
                                <span wire:loading wire:target="cancelRotationData"
                                    class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Cancel
                            </button>
                        </div>

                    @endif

                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.js" data-navigate-once></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" data-navigate-once></script>

    @script
        <script>
            (function() {
                document.addEventListener($wire.id + ':zones-updated', event => {
                    initSortable();
                });

                function initSortable() {
                    if ($(".rotation-form-div").data('ui-sortable')) {
                        $(".rotation-form-div").sortable('destroy');
                    }

                    $(".rotation-form-div").sortable({
                        handle: ".drag-icon",
                        sort: function(evt, ui) {},
                        start: function(event, ui) {},
                        stop: function(event, ui) {
                            let order = [];
                            document.querySelectorAll('.rotation-ind-div').forEach((v) => {
                                order.push(v.dataset.index)
                            })

                            $wire.sortRotationItems(order);
                        }
                    });
                }
            })()
        </script>
    @endscript
</div>
