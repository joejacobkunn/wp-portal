<div class="form-group x-media">

    @php
        $key = ($key ?? "") . $model . "_media-field";
        $fieldId = $fieldId ?? 'media_field_' . $model;
    @endphp

    <livewire:x-media-attachment
        :fieldId="$fieldId"
        :model="$model"
        :entity="$entity"
        :collection="$collection"
        :editable="$editable ?? false"
        :multiple="$multiple ?? false"
        :extra-field-view="$extraFieldView ?? ''"
        :listener="$listener ?? 'fieldUpdated'"
        :media-rules="$rules ?? ''"
        :key="$key ?? ''"
        :view-type="$viewType ?? 'grid'"
        :list-view="$listView ?? ''"
        :grid-view="$gridView ?? ''"
        :grid-width="$gridWidth ?? 25"
        parentComponent="{{ $parentComponent ?? $this::class }}"
    >

    @if(isset($model))
        @error($model) <span class="text-danger">{{ $message }}</span> @enderror
    @endif
</div>