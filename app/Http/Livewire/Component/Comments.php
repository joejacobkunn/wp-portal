<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;

class Comments extends Component
{
    /*
    |--------------------------------------------------------------------------
    | Configurable Attributes 
    |--------------------------------------------------------------------------
    */

    /**
     * Configured entity (Eloquent Model)
     */
    public $entity;

    /**
     * Default load limit
     */
    public $perPage = 3;

    /**
     * Display order
     */
    public $order = 'desc';

    public $deferLoad = false;

    public $alert = '';


    /*
    |--------------------------------------------------------------------------
    | Non-Configurable Attributes 
    |--------------------------------------------------------------------------
    */

    /**
     * For saving new comment
     */
    public $comment;

    /**
     * Comment List
     */
    public $comments = [];

    /**
     * Pagination for records
     */
    public $nextPage = 1;

    protected $rules = [
        'comment' => 'required|min:2',
    ];

    protected $listeners = [
        'load-more-comments' => 'loadMoreComments'
    ];

    public function createComment()
    {
        $this->validate();

        $comment = $this->entity->comments()->create(
            [
                'user_id' => auth()->user()->id,
                'comment' => $this->comment
            ]
        );

        $this->comment = '';
        $this->comments = [];
        $this->nextPage = 1;
        $this->loadComments(true);
        $this->dispatch("newCommentCreated", $comment);
    }

    public function loadComments($refresh = false)
    {
        if($refresh) {
            $this->comments = [];
            $this->nextPage = 1;
        }

        $comments = $this->entity
            ->comments()
            ->with([
                'user' => function ($query) {
                    return $query->basicSelect();
                },
            ])
            ->orderBy('created_at', $this->order)
            ->paginate($this->perPage, ['*'], 'page', $this->nextPage)
            ->through(function ($comment, $key) {
                $comment->created_at_string = $comment->created_at->diffForHumans();
                return $comment;
             });
            
        $this->comments = array_merge($this->comments, $comments->items());

        if ($comments->hasMorePages()) {
            $this->nextPage += 1;
        } else {
            $this->nextPage = null;
        }
    }

    public function mount()
    {
        if (!$this->deferLoad) {
            $this->loadComments();
        }
    }

    public function render()
    {
        return view('livewire.component.comments');
    }
}
