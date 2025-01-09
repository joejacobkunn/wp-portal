@php
// List of all months
$months = [
        'january', 'february', 'march', 'april', 'may', 'june',
        'july', 'august', 'september', 'october', 'november', 'december'
    ];
@endphp

<ul class="list-group">
@foreach ($months as $month)
    <li class="list-group-item">
            <h5 class="mb-0">{{ ucfirst($month) }}</h5>
        <div class="">
            @if ( isset($shifts->shift[$month]))

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Shift</th>
                            <th>Slot</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($shifts->shift[$month]))
                            @foreach ($shifts->shift[$month] as $day => $data)
                                @php $first = true; @endphp
                                @foreach ($data as $item)
                                    <tr>
                                        @if($first)
                                            <td rowspan="{{ count($data) }}">{{ $day }}</td>
                                            @php $first = false; @endphp
                                        @endif
                                        <td>{{ $item['shift'] }}</td>
                                        <td>{{ $item['slots'] }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endif
                    </tbody>
                </table>
            @else
                <p class="text-muted">No data available for {{ $month }}.</p>
            @endif
        </div>
    </li>
@endforeach
</ul>
