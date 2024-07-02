<div>
    <div wire:loading>
        Processing Payment...
    </div>
        @forelse ($userMessages as $message)
        <div class="card border-0 shadow p-4 mb-4 comment-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="font-small">
                    <a href="#">
                        <i class="fas fa-user"></i> <span
                            class="fw-bold">{{ $message['name'] }}</span>
                    </a>
                    <span class="fw-normal ms-2">{{ $message['created_at'] }}</span></span>
                <div class="d-none d-sm-block">
                </div>
            </div>
            <p class="m-0">{{ $message['message'] }}</p>
        </div>
        @empty
        <p></p>
    @endforelse

    <center>
        <button wire:click="loadComments()" class="btn btn-sm btn-gray-200 mb-2" type="button">
            <div wire:loading>
                <span class="spinner-border spinner-border-sm" role="status"
                    aria-hidden="true"></span>
            </div>
            Load More Comments
        </button>
    </center>

</div>
