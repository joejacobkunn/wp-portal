<div>
    @if(!empty($label))
        <h6 class="mb-3">{{ $label }}</h6>
    @endif
    
    <div class="row">
        @foreach ($renderedItems as $item)
        <div class="{{ $cols }} mb-3">
            <x-forms.radio
                :label="$item['label']"
                :name="$name"
                :value="$item['value']"
                :model="$model"
            />
        </div>
        @endforeach
    </div>

    @if(isset($model))
        @error($model) <span class="text-danger">{{ $message }}</span> @enderror
    @endif
</div>