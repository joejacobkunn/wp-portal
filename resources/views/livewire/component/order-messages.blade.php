
<div class="order-message container mt-4">
    <div class="d-flex justify-content-end mt-3">
        {{ $messages->links('vendor.pagination.custom-pagination') }}
    </div>
    <div class="list-group">

        @foreach($messages as $message )
            <div class="list-group-item inbox-item">
                <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">{{ $message->subject }} #{{ $message->order_number}}
                        <span class="badge bg-light-success">{{ $message->medium }}</span>
                        <span class="badge bg-light-success">{{ strtolower($message->status) }}</span></h5>
                    <h6>Email : {{$message->contact}}</h6>
                    <p class="inbox-content mb-1">{{ $message->content }}</p>
                </div>
                <div>
                    <small>{{ $message->created_at->diffForHumans() }}</small>
                </div>
                </div>
            </div>
        @endforeach
  </div>
