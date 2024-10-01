
<div class="order-message container mt-4">
    <div class="d-flex justify-content-end mt-3">
        {{ $messages->links('vendor.pagination.custom-pagination') }}
    </div>
    <div class="list-group">

        @foreach($messages as $message )
            <div class="list-group-item inbox-item">
                <div class="d-flex justify-content-between align-items-center">
                <div>
                   <a  href="#" wire:click.prevent="viewMessage({{ $message->id }})" > <h5 class="mb-1">
                        {{ $message->subject }} #{{ $message->order_number}}</h5></a>
                    <p>Email : {{$message->contact}}</p>
                </div>
                <div>
                    <small>{{ $message->created_at->diffForHumans() }}</small>
                </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="order-message-model">
        <x-modal :toggle="$orderMessageModal" size="lg" :closeEvent="'closeOrderMessage'">
            <x-slot name="title">Order Message: #{{ $selectedMessage?->order_number }}</x-slot>
            <div class="row w-100">
                <div class="col-md-12">
                    @if ($selectedMessage)
                        <h5>{{ $selectedMessage->subject }}
                             <span class="badge bg-light-info">{{ $message->medium }}</span>
                            <span class="badge bg-light-info">{{ strtolower($message->status) }}</span>
                        </h5>
                        <p>Email: {{ $selectedMessage->contact }}</p>
                        <hr>
                        <h5>Message : </h5>
                        <p>{{ $selectedMessage->content }}</p>
                    @endif
                    <hr>

                </div>

            </div>
            <div class="mt-2 float-start">
                <p><small>Created: {{ $selectedMessage?->created_at->diffForHumans() }}</small></p>

            </div>
        </x-modal>
    </div>

</div>
