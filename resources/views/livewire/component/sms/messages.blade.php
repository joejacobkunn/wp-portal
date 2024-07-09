<div>
    <div class="card border-0 shadow mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Messages</h5>
            @forelse ($userMessages as $message)
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="font-small">
                            <a href="#">
                                <i class="fas fa-user"></i>
                                <span class="fw-bold">{{ $message['name'] }}</span>
                            </a>
                            <span class="fw-normal ms-2">{{ $message['created_at'] }}</span>
                        </span>
                    </div>
                    <p class="m-0">{{ $message['message'] }}</p>
                </div>
            @empty
                <p>No messages yet.</p>
            @endforelse
            <center>
                <button wire:click="loadMessages()" class="btn btn-sm btn-gray-200 mb-2" type="button">
                    <div wire:loading wire:target="loadMessages">
                        <span class="spinner-border spinner-border-sm" role="status"
                            aria-hidden="true"></span>
                    </div>
                    Load More Comments
                </button>
            </center>
        </div>
    </div>

    <!-- New message input section -->
    <div class="card border-0 shadow p-4 mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Send a Message</h5>
            <form wire:submit.prevent="sendMessage">
                <div class="mb-3">
                    <textarea
                        wire:model="newMessage"
                        class="form-control"
                        rows="3"
                        placeholder="Type your message here..."
                    ></textarea>
                </div>
                <div class="mb-3">
                    <label for="attachment" class="form-label">Attachment</label>
                    <input type="file" class="form-control" id="attachment" wire:model="attachment">
                    @error('attachment') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        @if(true)
                            <span class="text-muted">File selected: {{ 'filename.csv'}}</span>
                        @endif
                    </div>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        wire:loading.attr="disabled"
                        wire:target="sendMessage, attachment"
                    >
                        <span wire:loading.remove wire:target="sendMessage, attachment">
                            <i class="fas fa-paper-plane me-2"></i>Send Message
                        </span>
                        <span wire:loading wire:target="sendMessage, attachment">
                            <i class="fas fa-spinner fa-spin me-2"></i>Sending...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
