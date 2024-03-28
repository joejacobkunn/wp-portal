<div class="form-group {{ $errors->has($model) ? 'is-invalid' : '' }}">
    <label>{{ $label ?? '' }}</label>

    <livewire:x-forms-htmleditor :field-id="$id ?? ''" :model="$model ?? ''" :value="$value ?? ''" :height="$height ?? 200"
        :listener="$listener ?? 'fieldUpdated'" parentComponent="{{ $parentComponent ?? $this::class }}" :key="$key ?? 'editor-' . now()" />

    @if (isset($model))
        @error($model)
            <span class="text-danger">{{ $message }}</span>
        @enderror
    @endif
</div>
