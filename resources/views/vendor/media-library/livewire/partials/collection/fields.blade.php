<div @class(['media-library-field', 'media-library-hidden' => ! $editableName])>
    @if($editableName)
        <label class="media-library-label">{{ __("Name") }}</label>
        <input
            dusk="media-library-field-name"
            class="media-library-input"
            type="text"
            name="{{ $mediaItem->propertyAttributeName('name') }}"
            value="{{ $mediaItem->name }}"
            wire:model="media.{{ $mediaItem->uuid }}.name"
        />
    @endif

    @error($mediaItem->propertyErrorName('name'))
        <p class="media-library-field-error">
               {{ $message }}
        </p>
    @enderror
</div>
