<div class="media-library-thumb" dusk="thumb">
    @if($mediaItem->preview_url)
        <img
            class="media-library-thumb-img" src="{{ $mediaItem->preview_url }}"
            alt="{{ $mediaItem->file_name }}">
    @else
        <span class="media-library-thumb-extension">
            <span
                class="media-library-thumb-extension-truncate">{{ $mediaItem->extension }}</span>
        </span>
    @endif

    <livewire:media-library-uploader
        :key="'thumb-uploader' . $mediaItem->uuid"
        :name="$this->name"
        :rules="$rules"
        :accept="$accept ?? []"
        :uuid="$mediaItem->uuid"
    />
</div>
