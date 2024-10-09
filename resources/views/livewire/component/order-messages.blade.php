<div class="card">

    <div class="card-body">
        <h4>Message Thread</h4>
        <div class="order-message container mt-2">
            <div class="d-flex justify-content-end mt-2">
                {{ $messages->links('vendor.pagination.custom-pagination') }}
            </div>
            <div class="list-group">

                @forelse ($messages as $message)
                    <div class="list-group-item inbox-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="#" wire:click.prevent="viewMessage({{ $message->id }})">

                                    <h5 class="mb-1 link-primary">
                                        @if ($message->medium == 'email')
                                            <i class="far fa-envelope"></i>
                                        @endif
                                        @if ($message->medium == 'sms')
                                            <i class="far fa-comment-dots"></i>
                                        @endif
                                        {{ $message->subject }}
                                        @if (strtolower($message->status) == 'cancelled')
                                            <span class="badge bg-light-danger">{{ ucwords($message->status) }}</span>
                                        @endif
                                        @if (strtolower($message->status) == 'follow up')
                                            <span class="badge bg-light-info">{{ ucwords($message->status) }}</span>
                                        @endif


                                    </h5>
                                </a>
                                <p>{!! Illuminate\Support\Str::limit($message->content, 125) !!}</p>
                                <small>Sent to {{ $message->contact }}</small>
                            </div>
                            <div>
                                <small>{{ $message->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                @empty
                    <p>No messages</p>
                @endforelse
            </div>

            <div class="order-message-model">
                <x-modal :toggle="$orderMessageModal" size="lg" :closeEvent="'closeOrderMessage'">
                    <x-slot name="title">Message</x-slot>
                    <div class="row w-100">
                        <div class="col-md-12">
                            @if ($selectedMessage)
                                <h5>{{ $selectedMessage->subject }}
                                </h5>
                                <p>To: {{ $selectedMessage->contact }}</p>
                                <hr>
                                <h5>Message : </h5>
                                <p>{!! $selectedMessage->content !!}</p>
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

    </div>
</div>
