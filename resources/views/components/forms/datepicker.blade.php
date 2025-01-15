<div class="form-group {{ $errors->has($model) ? 'is-invalid' : '' }}">

    @if(!isset($noLabel))
        <label>{{ $label ?? '' }}</label>
    @endif
    
    <livewire:x-forms-datepicker
        :model="$model"
        :value="$value ?? ''"
        :format="$format ?? ''"
        :icon="$icon ?? 'fa-calendar-day'"
        :placeholder="$placeholder ?? ''"
        :minDate="$minDate ?? ''"
        :maxDate="$maxDate ?? ''"
        :enableTime="isset($enableTime) ? filter_var($enableTime, FILTER_VALIDATE_BOOL) : false"
        :disabled="isset($disabled) ? filter_var($disabled, FILTER_VALIDATE_BOOL) : false"
        :readonly="isset($readonly) ? filter_var($readonly, FILTER_VALIDATE_BOOL) : true"
        :hideIcon="isset($hideIcon) ? filter_var($hideIcon, FILTER_VALIDATE_BOOL) : false"
        :clearable="isset($clearable) ? filter_var($clearable, FILTER_VALIDATE_BOOL) : false"
        :listener="$listener ?? 'fieldUpdated'"
        parentComponent="{{ $parentComponent ?? $this::class }}"
        :key="($key ?? $model).'datepicker'"
    />

    @if(isset($model))
        @error($model) <span class="text-danger">{{ $message }}</span> @enderror
    @endif
</div>