<div class="row">
    <div class="col-md-12 mb-3 csv-table-col" >
            <div class="table-responsive overflow-auto csv-table-wrap">
                        <table id="csv-table" class="table table-bordered" >
                            <thead>
                                <!-- for header -->
                                @if(isset($rows[0]))
                                    <tr>
                                        @foreach($rows[0] as $header)
                                            <th>{{ $header }}</th>
                                        @endforeach
                                    </tr>
                                @endif
                            </thead>
                            <tbody>
                                @foreach(array_slice($rows, 1) as $row)
                                    <tr>
                                        @foreach($row as $cell)
                                            <td>{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
            </div>
    </div>
</div>

