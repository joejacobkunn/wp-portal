<div>
    <input
        data-uuid
        type="hidden"
        name="{{ $mediaItem->propertyAttributeName('uuid') }}"
        value="{{ $mediaItem->uuid }}"
    />

    <input
        type="hidden"
        name="{{ $mediaItem->propertyAttributeName('preview_url') }}"
        value="{{ $mediaItem->preview_url }}"
    />

    <input
        type="hidden"
        name="{{ $mediaItem->propertyAttributeName('file_name') }}"
        value="{{ $mediaItem->file_name }}"
    />

    <input
        type="hidden"
        name="{{ $mediaItem->propertyAttributeName('size') }}"
        value="{{ $mediaItem->size }}"
    />

    <input
        type="hidden"
        name="{{ $mediaItem->propertyAttributeName('order') }}"
        value="{{ $mediaItem->order ?? 0 }}"
        data-order
    />
</div>
