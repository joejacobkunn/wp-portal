<div class="form-group x-media">

    @php
        $key = ($key ?? "") . $model . "_media-field";
    @endphp

    <livewire:x-media-attachment
        :model="$model"
        :entity="$entity"
        :collection="$collection"
        :editable="$editable ?? false"
        :multiple="$multiple ?? false"
        :extra-field-view="$extraFieldView ?? ''"
        :listener="$listener ?? 'fieldUpdated'"
        :rules="$rules ?? ''"
        :key="$key ?? ''"
        :view-type="$viewType ?? 'grid'"
        :list-view="$listView ?? ''"
        :grid-view="$gridView ?? ''"
        :grid-width="$gridWidth ?? 25"
    >

    @if(isset($model))
        @error($model) <span class="text-danger">{{ $message }}</span> @enderror
    @endif
</div>
