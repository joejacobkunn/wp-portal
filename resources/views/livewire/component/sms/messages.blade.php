<div wire:init="loadData">
    <div class="text-center w-100" wire:loading wire:target="loadData">
        @include('components.grow-spinner', ['message' => 'fetching user data'])
    </div>
    <div wire:loading.remove wire:target="loadData">
        <h5 class="card-title mb-3">Messages</h5>
           <div class="sms-container">
               @forelse ($userMessages as $message)
                   <div class="mb-3 pb-3 ">
                       <div class="d-flex justify-content-between align-items-center mb-2">
                           <span class="font-small">
                               <a href="#">
                                   <i class="fas fa-user"></i>
                                   <span class="fw-bold">{{ !empty($message['name']) ? $message['name'] : $apiUser['phone'] }}</span>
                               </a>
                               <span class="fw-normal ms-2" title="{{ $message['created_at'] }}">{{ \Carbon\Carbon::parse($message['created_at'])->diffForHumans() }}</span>
                           </span>
                       </div>
                       <p class="m-0">{{ $message['message'] }}</p>
                   </div>
               @empty
                   <p>No messages yet.</p>
               @endforelse
           </div>
           @if ($loadMoreBtn)
               <center>
                   <button wire:click="loadmessage({{ ++$messageOffset }}, {{$messageLimit}})" class="btn btn-sm btn-gray-200 mb-2" type="button">
                       <div wire:loading wire:target="loadmessage">
                           <span class="spinner-border spinner-border-sm" role="status"
                               aria-hidden="true"></span>
                       </div>
                       Load More Messages
                   </button>
               </center>
           @endif
           <hr>
           <h5 class="card-title mb-3">Send a Message</h5>
           <form wire:submit.prevent="sendMessage">
               <div class="mb-3">
                   <x-forms.textarea label="Message" model="newMessage" rows="3" lazy />
               </div>
               <div class="d-flex justify-content-start align-items-center">
                   <button
                       type="submit"
                       class="btn btn-primary"
                       wire:loading.attr="disabled"
                       wire:target="sendMessage">
                       <span wire:loading.remove wire:target="sendMessage">
                           <i class="fas fa-paper-plane me-2"></i>Send Message
                       </span>
                       <span wire:loading wire:target="sendMessage">
                           <i class="fas fa-spinner fa-spin me-2"></i>Sending...
                       </span>
                   </button>
               </div>
           </form>
    </div>
</div>
