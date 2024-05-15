<x-modal :toggle="$wtModal" size="xl">
    <x-slot name="title">
        Initiate Warehouse Transfer for Line Item {{ $backorder_line_info['prod'] ?? '' }}
    </x-slot>

    @if ($wt_transfer_number)
        <div class="alert alert-light-success color-success"><i class="bi bi-check-circle"></i> Warehouse Transfer was
            successful, your WT number is <strong>{{ $wt_transfer_number }}</strong>. There is no tie currently to this
            transfer yet.</div>
    @else
        <div class="row">
            <div class="card">
                <div class="card-body">
                    @if (!empty($line_item_inventory))

                        <div class="alert alert-light-warning color-warning">
                            Backordered at
                            <strong>{{ strtoupper($backorder_line_info['whse']) }} by
                                {{ $backorder_line_info['backorder_count'] }}
                                qty(s)</strong>
                        </div>

                        @if ($wt_whse)
                            <div style="">
                                <div class="card">
                                    <div class="card-body">
                                        <p>This will transfer
                                            <strong>{{ $backorder_line_info['backorder_count'] }}</strong> of
                                            {{ strtoupper($backorder_line_info['prod']) ?? '' }} from
                                            <strong>{{ strtoupper($wt_whse) }}</strong> to
                                            <strong>{{ strtoupper($backorder_line_info['whse']) }}</strong>. System will
                                            attempt to tie the transfer as well.
                                            Fill out the
                                            fields below to
                                            initaite transfer.
                                        </p>
                                        <div class="row">
                                            <div class="col-lg-4 mb-1">
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text">Due Date</span>
                                                    <input wire:model='wt_due_date' type="date" class="form-control"
                                                        placeholder="Due Date">
                                                </div>
                                                @error('wt_due_date')
                                                    <span class="text-danger">{{ $errors->first('wt_due_date') }}</span>
                                                @enderror


                                            </div>
                                            <div class="col-lg-4 mb-1">
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text">Reqd Ship Date</span>
                                                    <input wire:model='wt_req_ship_date' type="date"
                                                        class="form-control" placeholder="Due Date">
                                                </div>
                                                @error('wt_req_ship_date')
                                                    <span
                                                        class="text-danger">{{ $errors->first('wt_req_ship_date') }}</span>
                                                @enderror

                                            </div>
                                            <div class="col-lg-4 mb-1">
                                                <button
                                                    wire:click="transferToWarehouse('{{ $wt_whse }}', '{{ $backorder_line_info['prod'] }}', '{{ $backorder_line_info['backorder_count'] }}')"
                                                    class="btn btn-primary"><i class="fas fa-exchange-alt"></i>
                                                    Transfer from
                                                    {{ $wt_whse }}
                                                    <div wire:loading
                                                        wire:target="transferToWarehouse('{{ $wt_whse }}', '{{ $backorder_line_info['prod'] }}', '{{ $backorder_line_info['backorder_count'] }}')">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                    </div>

                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif


                        <table class="table table-dark mb-0">
                            <thead>
                                <tr>
                                    <th>Warehouse</th>
                                    <th>Qty on Hand</th>
                                    <th>Qty Commit</th>
                                    <th>Qty Reserved</th>
                                    <th>Available</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($line_item_inventory as $inventory)
                                    @php $inventory_level = $inventory->qtyonhand - ($inventory->qtycommit + $inventory->qtyreservd) @endphp
                                    <tr @if ($inventory_level >= $backorder_line_info['backorder_count']) class="table-success" @endif>
                                        <td>{{ strtoupper($inventory->whse) }}</td>
                                        <td>{{ $inventory->qtyonhand }}</td>
                                        <td @if ($inventory->qtycommit > 0) class="text-danger" @endif>
                                            {{ $inventory->qtycommit }}</td>
                                        <td @if ($inventory->qtyreservd > 0) class="text-danger" @endif>
                                            {{ $inventory->qtyreservd }}</td>
                                        <td @if ($inventory_level > 0) class="text-success" @endif>
                                            <strong>{{ $inventory_level }}</strong>
                                            @if ($inventory_level >= $backorder_line_info['backorder_count'])
                                                <button wire:click="setWarehouseTransfer('{{ $inventory->whse }}')"
                                                    class="btn btn-sm btn-outline-success float-end"><i
                                                        class="fas fa-plus"></i> WT
                                                    <div wire:loading
                                                        wire:target="setWarehouseTransfer('{{ $inventory->whse }}')">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                    </div>

                                                </button>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-light-warning color-warning"><i class="bi bi-exclamation-triangle"></i>
                            Product not available at any Warehouse</div>
                    @endif

                </div>
            </div>
        </div>


    @endif


</x-modal>
