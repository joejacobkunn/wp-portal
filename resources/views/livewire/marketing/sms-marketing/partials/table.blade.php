<div class="col-md-12 mb-3 csv-table-col" >
    <div class="table-responsive overflow-auto csv-table-wrap">
        <table id="csv-table" class="table table-bordered" >
            <thead>
                <!-- for header -->
                    <tr>
                        <th style="width:30%">Phone</th>
                        <th style="width: 70%">Message</th>
                    </tr>
            </thead>
            <tbody>
                @forelse($records as $cell)
                    <tr>
                        <td style="width: 30%">{{ $cell['phone'] }}</td>
                        <td style="width: 70%">{{ $cell['message'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">No valid records are available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
