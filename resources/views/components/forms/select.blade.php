<div class="form-group {{ $errors->has($model) ? 'is-invalid' : '' }}">
    @if(!empty($label))
        <label>{{ $label ?? '' }}</label>
    @endif
    <livewire:x-select-field
        :field-id="$model ?? 'field'"
        :options="$options"
        :selected="$selected ?? []"
        :placeholder="$placeholder ?? 'Please Select'"
        :defaultOption="isset($defaultOption) ? filter_var($defaultOption, FILTER_VALIDATE_BOOL) : true"
        :defaultOptionLabel="$defaultOptionLabel ?? 'Please Select'"
        :defaultOptionSelectable="isset($defaultOptionSelectable) ? filter_var($defaultOptionSelectable, FILTER_VALIDATE_BOOL) : false"
        :listener="$listener ?? 'fieldUpdated'"
        :multiple="$multiple ?? false"
        :multiple="isset($multiple) ? filter_var($multiple, FILTER_VALIDATE_BOOL) : false"
        :disabled="isset($disabled) ? filter_var($disabled, FILTER_VALIDATE_BOOL) : false"
        :key="($key ?? $model) . '-select'"
        :label-index="$labelIndex ?? 'text'"
        :value-index="$valueIndex ?? 'value'"
        :select-all-option="isset($selectAllOption) ? filter_var($selectAllOption, FILTER_VALIDATE_BOOL) : true"
        :hide-search="isset($hideSearch) ? filter_var($hideSearch, FILTER_VALIDATE_BOOL) : false"
        :no-result-text="$noResultText ?? ''"
        :search-placeholder="$searchPlaceholder ?? ''"
        :direction="$direction ?? 'auto'"
        :hasAssociativeIndex="$hasAssociativeIndex ?? false"
        :allowDeselect="isset($allowDeselect) ? filter_var($allowDeselect, FILTER_VALIDATE_BOOL) : true"
        parentComponent="{{ $parentComponent ?? $this::class }}"
    >

    @if(isset($model))
        @error($model) <span class="text-danger">{{ $message }}</span> @enderror
    @endif
</div>
