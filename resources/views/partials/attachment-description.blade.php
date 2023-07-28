@include('media-library::livewire.partials.collection.fields')

<div class="media-library-field">
    <label class="media-library-label">Description</label>
    <input
        class="media-library-input"
        type="text"
        {{ $mediaItem->livewireCustomPropertyAttributes('description') }}
    />

    @error($mediaItem->customPropertyErrorName('description'))
    <span class="media-library-text-error">
       {{ $message }}
    </span>
    @enderror
</div>
