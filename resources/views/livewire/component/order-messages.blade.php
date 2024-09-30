
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


      <div class="list-group-item inbox-item">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-1">Ecommerce website Paypal integration </h5>
            <p class="inbox-content mb-1">We will start the new application development soon...</p>
          </div>
          <small>2:15 AM</small>
        </div>
      </div>

      <div class="list-group-item inbox-item">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-1">How To Set Intentions That Energize You</h5>
            <p class="inbox-content mb-1">I will provide you more details after this Saturday...</p>
          </div>
          <small>2:13 AM</small>
        </div>
      </div>

      <div class="list-group-item inbox-item">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-1">Harness The Power Of Words In Your Life</h5>
            <p class="inbox-content mb-1">When the equation, first to ability the forwards...</p>
          </div>
          <small>Yesterday</small>
        </div>
      </div>

      <div class="list-group-item inbox-item">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-1">Helen Keller A Teller And A Seller</h5>
            <p class="inbox-content mb-1">Thanks for your feedback! Here's a new layout for...</p>
          </div>
          <small>1:15 PM</small>
        </div>
      </div>
    </div>

  </div>
