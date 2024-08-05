<div class="col-md-12 mb-3 csv-table-col" >
    <div class="table-responsive overflow-auto csv-table-wrap">
        <table id="csv-table" class="table table-bordered" >
            <thead>
                <!-- for header -->
                    <tr>
                        <th>Phone</th>
                        <th>Message</th>
                        <th>Office</th>
                    </tr>
            </thead>
            <tbody>
                @forelse($records as $cell)
                    <tr>
                        <td>{{ $cell['phone'] }}</td>
                        <td>{{ $cell['message'] }}</td>
                        <td>{{ $cell['office'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">No valid records are available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
