<div class="col-md-12 mb-3 csv-table-col" >
    <div class="table-responsive overflow-auto csv-table-wrap">
        <table id="csv-table" class="table table-bordered" >
            <thead>
                <!-- for header -->
                    <tr>
                        @foreach ($headers as $key => $value)
                            <th>{{ $value}}</th>
                        @endforeach
                    </tr>
            </thead>
            <tbody>
                @forelse($records as $cell)
                    <tr>
                        @foreach($headers as $key => $value)
                            <td>{{ $cell[$key] }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">No valid records are available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
