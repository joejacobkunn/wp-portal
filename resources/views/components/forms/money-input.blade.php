<div class="form-group {{ $errors->has($model) ? 'is-invalid' : '' }}">

    @if(!isset($noLabel))
        <label>{{ $label ?? '' }}</label>
    @endif

    <livewire:x-forms-moneyinput
        :model="$model"
        :value="$value ?? ''"
        :icon="$icon ?? 'fa-dollar-sign'"
        :placeholder="$placeholder ?? ''"
        :hideIcon="isset($hideIcon) ? filter_var($hideIcon, FILTER_VALIDATE_BOOL) : false"
        :keepFormat="isset($keepFormat) ? filter_var($keepFormat, FILTER_VALIDATE_BOOL) : false"
        :lazy="isset($lazy) ? filter_var($lazy, FILTER_VALIDATE_BOOL) : false"
        :disabled="isset($disabled) ? filter_var($disabled, FILTER_VALIDATE_BOOL) : false"
        :fractionDigits="$fractionDigits ?? 2"
        :listener="$listener ?? 'fieldUpdated'"
        parentComponent="{{ $parentComponent ?? $this::class }}"
        :key="($key ?? $model).'moneyinput'"
    />

    @if(isset($model))
        @error($model) <span class="text-danger">{{ $message }}</span> @enderror
    @endif
</div>