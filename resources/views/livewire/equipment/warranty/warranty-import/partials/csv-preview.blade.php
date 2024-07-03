<div>
    <div class="col-md-12 mb-3">
        <h3>Preview</h3>
    </div>
@if ( $csvErrorCount>0 )
    <div class="col-md-12 mb-3">
        <div class="alert alert-warning" role="alert">
            <i class="fas fa-exclamation-circle"></i> Match Not Found for {{ $csvErrorCount }} Rows
        </div>
    </div>
@endif
    <div class="col-md-12 mb-3 csv-table-col" >
            <div class="table-responsive overflow-auto csv-table-wrap">
                        <table id="csv-table" class="table table-bordered" >
                            <thead>
                                <!-- for header -->
                                @if(isset($rows[0]))
                                    <tr>
                                        @foreach($rows[0]['data'] as $header)
                                            <th>{{ $header }}</th>
                                        @endforeach
                                    </tr>
                                @endif
                            </thead>
                            <tbody>
                                @foreach(array_slice($rows, 1) as $row)
                                    <tr class="{{ !$row['status'] ? 'table-danger' : '' }}">
                                        @foreach($row['data'] as $cell)
                                            <td>{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
            </div>
    </div>
</div>

