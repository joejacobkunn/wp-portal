<div class="form-group {{ $errors->has($model) ? 'is-invalid' : '' }}">

    @php
        $id = !empty($id) ? $id : str_replace('.', '_', $model . "_editor");
    @endphp

    @if(!isset($noLabel))
        <label>{{ $label ?? '' }}</label>
    @endif

    <livewire:x-forms-htmleditor
        :field-id="$id"
        :model="$model ?? ''"
        :value="$value ?? ''"
        :height="$height ?? 150"
        :maxLength="$maxLength ?? '-1'"
        :placeholder="$placeholder ?? ''"
        :listener="$listener ?? 'fieldUpdated'"
        :showCharCount="isset($showCharCount) ? filter_var($showCharCount, FILTER_VALIDATE_BOOL) : false"
        :strictCount="isset($strictCount) ? filter_var($strictCount, FILTER_VALIDATE_BOOL) : false"
        parentComponent="{{ $parentComponent ?? $this::class }}"
        :key="($key ?? 'text').'editor'"
    />

    @if(isset($model))
        @error($model) <span class="text-danger">{{ $message }}</span> @enderror
    @endif
</div>