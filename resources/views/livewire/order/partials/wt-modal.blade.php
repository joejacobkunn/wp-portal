<x-modal :toggle="$wtModal" size="lg">
    <x-slot name="title">
        Initiate Warehouse Transfer for Line Item {{ $backorder_line_info['prod'] ?? '' }}
    </x-slot>

    <div class="row">
        <div class="card">
            <div class="card-body">
                @if (!empty($line_item_inventory))

                    <div class="alert alert-light-warning color-warning">
                        Backordered at
                        <strong>{{ $backorder_line_info['whse'] }} by {{ $backorder_line_info['backorder_count'] }}
                            qty(s)</strong>
                    </div>

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
                                    <td>{{ $inventory->whse }}</td>
                                    <td>{{ $inventory->qtyonhand }}</td>
                                    <td>{{ $inventory->qtycommit }}</td>
                                    <td>{{ $inventory->qtyreservd }}</td>
                                    <td>{{ $inventory_level }}
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

</x-modal>
