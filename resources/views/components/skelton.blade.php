<div class="loading-skeleton">
    @if(!empty($type) && $type=='table')
        <div class="d-flex flex-column">
            <div class="d-md-flex justify-content-between mb-3">
                <div class="d-md-flex">
                    <div class="mb-3 mb-md-0 input-group">
                        <input type="text" class="form-control">
                    </div>
                </div>

                <div class="d-md-flex">
                    <div class=" mb-3 mb-md-0 md-0 ms-md-2">
                        <div class="dropdown d-block d-md-inline" wire:key="column-select-button-table">
                            <button class="btn btn-outline-secondary dropdown-toggle d-block w-100 d-md-inline" type="button" id="columnSelect-table" aria-haspopup="true">
                                dummy text </button>

                            <div class="dropdown-menu dropdown-menu-end w-100" aria-labelledby="columnSelect-table">
                                <div class="form-check ms-2">
                                    <input checked="" wire:click="deselectAllColumns" wire:loading.attr="disabled" type="checkbox" class="form-check-input">
                                    <label wire:loading.attr="disabled" class="form-check-label">
                                        dummy text
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ms-0 ms-md-2">
                        <select wire:model="perPage" id="perPage" class="form-select">
                            <option value="25" wire:key="per-page-25-table">25</option>
                            <option value="50" wire:key="per-page-50-table">50</option>
                            <option value="100" wire:key="per-page-100-table">100</option>
                        </select>
                    </div>

                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="">
                    <tr class="table-secondary">
                        <th scope="col" class="" wire:key="header-col-0-ArrXUj1DEHDfCfj2wLPl">
                            <div class="d-flex align-items-center" wire:click="sortBy('id')" style="cursor:pointer;">
                                <span>dummy text</span>
                            </div>
                        </th>

                        <th scope="col" class="" wire:key="header-col-1-ArrXUj1DEHDfCfj2wLPl">
                            <div class="d-flex align-items-center" wire:click="sortBy('name')" style="cursor:pointer;">
                                <span>dummy text</span>
                            </div>
                        </th>

                        <th scope="col" class="" wire:key="header-col-2-ArrXUj1DEHDfCfj2wLPl">
                            <div class="d-flex align-items-center" wire:click="sortBy('subdomain')" style="cursor:pointer;">
                                <span>dummy text</span>
                            </div>
                        </th>

                        <th scope="col" class="" wire:key="header-col-3-ArrXUj1DEHDfCfj2wLPl">
                            <div class="d-flex align-items-center" wire:click="sortBy('is_active')" style="cursor:pointer;">
                                <span>dummy text</span>
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody class="">
                    @for($i=0 ; $i < 10; $i++) <tr>
                        <td class="" wire:key="cell-0-0-ArrXUj1DEHDfCfj2wLPl">
                            dummy text
                        </td>

                        <td class="" wire:key="cell-0-1-ArrXUj1DEHDfCfj2wLPl">
                            dummy text
                        </td>

                        <td class="" wire:key="cell-0-2-ArrXUj1DEHDfCfj2wLPl">
                            dummy text
                        </td>

                        <td class="" wire:key="cell-0-3-ArrXUj1DEHDfCfj2wLPl">
                            dummy text
                        </td>
                        </tr>
                        @endfor
                </tbody>

            </table>
        </div>
    @else
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <h4 class="card-title">Card With Header And Footer</h4>
                    <p class="card-text">
                        Gummies bonbon apple pie fruitcake icing biscuit apple pie jelly-o sweet roll. Toffee
                        sugar plum sugar plum jelly-o jujubes bonbon dessert carrot cake.
                    </p>
                    <p class="card-text">
                        Gummies bonbon apple pie fruitcake icing biscuit apple pie jelly-o sweet roll. Toffee
                        sugar plum sugar plum jelly-o jujubes bonbon dessert carrot cake.
                    </p>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <span>Card Footer</span>
                <button class="btn btn-light-primary">Read More</button>
            </div>
        </div>
    @endif
</div>