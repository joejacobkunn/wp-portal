<div class="row d-flex inventory-filter-section">
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <x-forms.select
                    model="filter_warehouse"
                    :options="$warehouses"
                    :selected="$filter_warehouse"
                    hasAssociativeIndex
                    default-option-label="Select Warehouse"
                    label-index="title"
                    value-index="short"
                    :key="'filter-warehouse-' . now()"
                    lazy
                />
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="form-group x-input">
                    <div class="input-group">
                        <input type="text"
                            class="form-control"
                            placeholder="Search"
                            wire:model.live.debounce.800ms="searchText"
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row justify-content-end">
            <div class="col-md-3">
                <x-forms.select model="orderBy"
                    :options="[
                        ['value' => 'oldest', 'name' => 'Oldest'],
                        ['value' => 'latest', 'name' => 'Latest'],
                    ]"
                    :selected="$orderBy ?? null"
                    default-option-label="Order By"
                    label-index="name"
                    value-index="value"
                />
            </div>
        </div>
    </div>
</div>
