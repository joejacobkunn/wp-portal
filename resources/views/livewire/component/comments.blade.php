<div>
     <div  {{ $deferLoad ? 'wire:init=loadComments' : '' }} wire:loading.class="loading-skeleton" wire:target="loadComments" class="card border-light shadow-sm mb-4">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
            <h3 class="h5 mb-0"><i class="fas fa-comment-dots"></i> Comments</h3>
            <div class="card-body">
                <form wire:submit.prevent="createComment()" class="mt-1 mb-4"><textarea class="form-control border-2 mb-4" wire:model.defer="comment"
                        placeholder="Your Comment" rows="3" maxlength="1000" required=""></textarea>
                        @error('comment') <span class="text-danger">{{ $message }}</span> @enderror 
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <div>
                            <button type="submit"
                                wire:loading.attr="disabled"
                                class="btn btn-secondary d-inline-flex align-items-center">
                                <div wire:loading wire:target="createComment">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                </div>
                                <svg
                                    class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg> 
                                Comment
                            </button>
                        </div>
                    </div>
                </form>

                <div>
                    
                    @forelse ($comments as $comment)
                        
                        <div class="card border-0 shadow p-4 mb-4 comment-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="font-small">
                                    <a href="#">
                                    <i class="fas fa-user"></i> <span class="fw-bold">{{$comment['user']['name'] }}</span> 
                                    </a>
                                    <span class="fw-normal ms-2">{{$comment['created_at_string']}}</span></span>
                                <div class="d-none d-sm-block">
                                </div>
                            </div>
                            <p class="m-0">{{$comment['comment']}}</p>
                        </div>
                    @empty
                        
                    @endforelse

                    @if($nextPage)
                        <center>
                            <button wire:click="loadComments()" class="btn btn-sm btn-gray-200 mb-2" type="button">
                                <div wire:loading>
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                </div>
                                Load More Comments
                            </button>
                        </center>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
