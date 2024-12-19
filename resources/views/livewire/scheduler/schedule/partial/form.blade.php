<div class="row">
    <div class="col-8 ">
        <div class="border rounded p-4">
            <form wire:submit.prevent="submit()">
                <div class="row w-100">
                    <div class="col-md-12">
                        <div class="form-group">
                            <x-forms.select label="Schedule" model="form.type" :options="$scheduleOptions" :hasAssociativeIndex="true"
                                default-option-label="- None -" :selected="$form->type" :key="'schedule-' . now()" />
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <x-forms.input type="number" label="Order Number" model="form.sx_ordernumber" lazy />
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <x-forms.input type="number" label="Order Number Suffix" model="form.suffix" lazy />
                        </div>
                    </div>
                    @if ($form->orderInfo)
                        <div class="col-md-12 mb-3">
                            <div class="alert alert-light-primary color-primary" role="alert">
                                <span class="badge bg-light-warning float-end"><a target="_blank" href=""><i
                                            class="fas fa-external-link-alt"></i> CustNo
                                        #{{ $form->orderInfo?->sx_customer_number }}</a></span>
                                <h4 class="alert-heading">Servicable Address</h4>
                                <p>
                                <address class="ms-1">
                                    <Strong>{{$form->orderInfo?->customer->name }}</Strong> <br>
                                    {{$form->orderInfo?->customer->address}}<br>
                                    {{$form->orderInfo?->customer->address2}}<br>
                                    {{$form->orderInfo?->customer->city}}, {{$form->orderInfo?->customer->state}} - ({{$form->orderInfo?->customer->zip}})<br>
                                    <i class="fa-solid fa-phone"></i> {{$form->orderInfo?->customer->phone ? $form->orderInfo?->customer->phone:'NA'}}<br>
                                    <i class="fa-solid fa-envelope"></i> {{$form->orderInfo?->customer->email ? $form->orderInfo?->customer->email:'NA'}}<br>

                                </address>
                                </p>
                                <hr>
                                <p class="mb-0">[Shipping Instructions] and [Shipto]</p>
                            </div>
                        </div>
                    @endif

                    @if ($form->orderInfo?->line_items)
                        <div class="col-md-12 mb-3">
                            <ul class="list-group">
                                <li class="list-group-item list-group-item-primary">Line Items</li>
                                @foreach ($form->orderInfo->line_items['line_items'] as $item)
                                    <li class="list-group-item">
                                        <x-forms.checkbox :label="$item['descrip'] . '(' . $item['shipprod'] . ')'" :name="'lineItems[]'" :value="$item['shipprod']"
                                            :model="'form.line_items'" />
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-forms.datepicker type="date" label="Schedule Date" model="form.schedule_date" lazy />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-forms.input type="time" prependIcon="fas fa-clock" label="Schedule Time"
                                model="form.schedule_time" lazy />
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    <button class="btn btn-primary" type="submit">
                        <div wire:loading wire:target="submit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        {{ $this->isEdit ? 'Update' : 'Schedule' }}
                    </button>

                </div>
            </form>
        </div>

    </div>
    <div class="col-4">
        <ul class="list-group">
            <li class="list-group-item list-group-item-primary">
                <span class="badge bg-light-warning float-end"><a href=""><i
                            class="fas fa-external-link-alt"></i>
                        #{{ $form->orderInfo?->order_number }}-{{ $form->orderInfo?->order_number_suffix }}</a></span>
                Order Info
            </li>
            @if ($form->orderInfo)
                <li class="list-group-item"><strong>Warehouse</strong> <span
                        class="float-end">{{ strtoupper($form->orderInfo->whse) }}</span></li>
                <li class="list-group-item"><strong>Status</strong> <span
                        class="float-end">{{ $form->orderInfo->status }}</span></li>
            @else
                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Order not selected </h3>
                    </div>
                </li>
            @endif
        </ul>
    </div>
</div>
