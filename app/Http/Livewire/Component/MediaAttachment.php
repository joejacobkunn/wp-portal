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

    public $fieldId;

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
    public $mediaRules = '';

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

    public $parentComponent;

    /*
    |--------------------------------------------------------------------------
    | Non-Configurable Attributes 
    |--------------------------------------------------------------------------
    */

    public $medias = [];

    public $mediaRendered = [];

    public $previewImage;

    public $fileVar;

    public $mediaComponentNames = [];

    /**
     * Custom listeners 
     */
    public function getListeners()
    {
        return array_merge(parent::getListeners(), [
            $this->fieldId.':mediaUpdated' => 'mediaUpdated',
        ]);
    }

    public function mount()
    {
        $this->loadMedia();
        $this->fileVar = uniqid();
    }

    /**
     * Render component template
     */
    public function render()
    {
        if (!$this->editable) {
            $this->medias = $this->entity->media()->where('collection_name', $this->collection)->get();
        }

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

        $this->mediaRendered = [];
        foreach ($this->medias as $media) {
            $this->mediaRendered[$media->uuid] =  [
                'name' => $media->name,
                'file_name' => $media->file_name,
                'uuid' => $media->uuid,
                'preview_url' => $media->hasGeneratedConversion('preview') ? $media->getUrl('preview') : '',
                'order' => $media->order_column,
                'custom_properties' => $media->custom_properties,
                'extension' => $media->extension,
                'size' => $media->size,
                'created_at' => $media->created_at->timestamp,
                'mime_type' => $media->mime_type,
                'extension' => pathinfo($media->file_name, PATHINFO_EXTENSION),
            ];
        }
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
        $this->dispatch("{$this->fileVar}:fileAdded", [
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
        $this->dispatch('browser:media-library:view-update', $viewType);
    }

    /**
     * Refresh media component
     */
    public function refreshMedia()
    {
        $this->dispatch('$refresh');
        $this->dispatch('contentChanged');
    }

    public function setValue($value)
    {
        $values = array_merge($this->mediaRendered, [
            $value['uuid'] => $value
        ]);

        
        $this->dispatch($this->listener, $this->model, $values)->to($this->parentComponent); 
    }

    public function mediaUpdated($items)
    {
        $mediaItems = [];
        foreach ($items as $item) {
            $item = $item[0] ?? [];

            if (isset($item['custom_properties']) && !empty($item['custom_properties'][0]['description'])) {
                $item['custom_properties'] = $item['custom_properties'][0];
            } else {
                $item['custom_properties'] = [];   
            }

            $mediaItems[] = $item;
        }
        
        $this->dispatch($this->listener, $this->model, array_filter($mediaItems))->to($this->parentComponent);
        
    }

}
