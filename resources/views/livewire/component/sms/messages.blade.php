<div>
         <h5 class="card-title mb-3">Messages</h5>
            <div class="messages-container" style="max-height: 400px; overflow-y: auto;">
                @forelse ($userMessages as $message)
                    <div class="mb-3 pb-3 ">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="font-small">
                                <a href="#">
                                    <i class="fas fa-user"></i>
                                    <span class="fw-bold">{{ !empty($message['name']) ? $message['name'] : $apiUser['phone'] }}</span>
                                </a>
                                <span class="fw-normal ms-2">{{ $message['created_at'] }}</span>
                            </span>
                        </div>
                        <p class="m-0">{{ $message['message'] }}</p>
                    </div>
                @empty
                    <p>No messages yet.</p>
                @endforelse
            </div>
            <center>
                <button wire:click="loadMoreMessages({{++$this->messageOffset}})" class="btn btn-sm btn-gray-200 mb-2" type="button">
                    <div wire:loading wire:target="loadMoreMessages">
                        <span class="spinner-border spinner-border-sm" role="status"
                            aria-hidden="true"></span>
                    </div>
                    Load More Messages
                </button>
            </center>
            <hr>
            <h5 class="card-title mb-3">Send a Message</h5>
            <form wire:submit.prevent="sendMessage">
                <div class="mb-3">
                    <x-forms.textarea label="Notes" model="newMessage" rows="5" lazy />
                </div>
                <div class="d-flex justify-content-end align-items-center">
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
