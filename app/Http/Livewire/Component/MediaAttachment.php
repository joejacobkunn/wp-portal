<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;
use App\Traits\WithMedia;

class MediaAttachment extends Component
{
    use WithMedia;

    /*
    |--------------------------------------------------------------------------
    | Configurable Attributes
    |--------------------------------------------------------------------------
    */

    /** Media libray collection Name */
    public $collection;

    /**
     * Parent component attribute
     */
    public $model;

    /** Eloquent Model */
    public $entity;

    /** Multiple upload */
    public $multiple = false;

    /**
     * MIME Rules
     *
     * eg: mimes:jpg,jpeg
     */
    public $rules = '';

    /** Show edit field */
    public $editable = false;

    /** Extra fields to display */
    public $extraFieldView;

    /** Media view layout type */
    public $viewType = 'grid';

    /** Media list view layout */
    public $listView;

    /** Media grid view layout */
    public $gridView;

    /**
     * Listener
     */
    public $listener = 'fieldUpdated';

    /**
     * Grid tile width
     */
    public $gridWidth = 25;

    /*
    |--------------------------------------------------------------------------
    | Non-Configurable Attributes
    |--------------------------------------------------------------------------
    */
    public $fieldId;

    public $medias = [];

    public $previewImage;

    public $fileVar;

    public $mediaComponentNames = [];

    /**
     * Custom listeners
     */
    public function getListeners()
    {
        return array_merge(parent::getListeners(), [
            'refreshMedia' => 'refreshMedia',
        ]);
    }

    /**
     * Render component template
     */
    public function render()
    {
        $this->fieldId = $this->collection . "-library-" . uniqid();

        return view('livewire.component.media-attachment');
    }

    /**
     * Load media from collection
     */
    public function loadMedia()
    {
        if (!$this->collection) {
            $this->collection = $this->entity->defaultCollection;
        }

        $this->medias = $this->entity->getMedia($this->collection);
    }

    /**
     * Show preview of record
     */
    public function showPreview($index)
    {
        $media = $this->medias[$index];
        $this->previewImage = $media;
    }

    /**
     * Close preview popup
     */
    public function closePreview()
    {
        $this->previewImage = null;
    }

    /**
     * Load mediaitem on x-media-library-attachment component (Invoked from JavaScript)
     */
    public function loadUploaderMedia()
    {
        if ($this->multiple || !isset($this->medias[0])) return;

        $media = $this->medias[0];
        $this->emit("{$this->fileVar}:fileAdded", [
            'name' => $media->name,
            'fileName' => $media->file_name,
            'oldUuid' => "",
            'uuid' => $media->uuid,
            'previewUrl' => $media->hasGeneratedConversion('preview') ? $media->getUrl('preview') : '',
            'order' => $media->order_column,
            'size' => $media->size,
            'mime_type' => $media->mime_type,
            'extension' => pathinfo($media->file_name, PATHINFO_EXTENSION),
        ]);
    }

    /**
     * Toggle media view layout
     */
    public function toggleView($viewType)
    {
        $this->viewType = $viewType;
    }

    /**
     * Refresh media component
     */
    public function refreshMedia()
    {
        $this->emit('$refresh');
        $this->dispatchBrowserEvent('contentChanged');
    }
}
