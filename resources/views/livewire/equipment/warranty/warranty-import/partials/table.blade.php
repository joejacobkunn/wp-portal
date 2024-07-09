<div class="col-md-12 mb-3 csv-table-col" >
    <div class="table-responsive overflow-auto csv-table-wrap">
        <table id="csv-table" class="table table-bordered" >
            <thead>
                <!-- for header -->
                    <tr>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Serial No</th>
                        <th>Reg Date</th>
                    </tr>
            </thead>
            <tbody>
                @forelse($records as $cell)
                    <tr>
                        <td>{{ $cell['brand'] }}</td>
                        <td>{{ $cell['model'] }}</td>
                        <td>{{ $cell['serial'] }}</td>
                        <td>{{ $cell['reg_date'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">No valid records are available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
