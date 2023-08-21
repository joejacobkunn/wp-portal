<x-modal :toggle="$service_plan_modal" size="xl">
    <x-slot name="title">
        <div class="">7YEPP History</div>
    </x-slot>
    <div class="row" wire:ignore>
        <div class="col-md-12">
            <div class="alert alert-light-primary color-primary">
                <i class="fas fa-info-circle"></i> <span class="ms-4"><strong>Model</strong> :
                    @if(!empty($this->service_plans))
                    {{ $this->service_plans[0]->ModelNo }}</span> @endif
                <span class="ms-4"><strong>Serial</strong> : @if(!empty($this->service_plans))
                    {{ $this->service_plans[0]->SerialNo }}</span> @endif
            </div>

            <div class="card border-secondary collapse-icon accordion-icon-rotate">
                <div class="card-body">
                    <div class="list-group">
                        @forelse ($this->service_plans as $service_plan)
                        <a href="#" class="list-group-item list-group-item-action" aria-current="true">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">
                                    <span class="badge bg-light-secondary">Customer No :
                                        {{intval($service_plan->CustNo)}}</span>
                                    <span class="badge bg-light-secondary">Order No :
                                        {{$service_plan->OrderNo}}-{{$service_plan->OrderSuf}}</span>
                                    <span class="badge bg-light-secondary">Labor Code
                                        : {{$service_plan->LaborCode}}</span>
                                    <span class="badge bg-light-secondary">Service Date
                                        : {{date('m-d-Y',strtotime($service_plan->InvoiceDt))}}</span>
                                </h5>
                            </div>
                        </a>
                        @empty
                        <div class="alert alert-light-warning color-warning">
                            No service plans for this equipment
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-modal>