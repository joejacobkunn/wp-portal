
@if ($form->zipcodeInfo)
    @foreach ($form->zipcodeInfo as $zipcode )
        <ul class="list-group mb-3">
            <li class="list-group-item list-group-item-primary">
                <span class="badge bg-light-warning float-end">

                    <a target="_blank"
                        href="{{ route('service-area.zipcode.show', ['zipcode' => $zipcode['id']]) }}"><i
                            class="fas fa-external-link-alt"></i>
                        #{{ $zipcode['zip_code'] }}</a>
                </span>
                ZIP Code Info # {{$zipcode['warehouse']['title']}}
            </li>
            <li class="list-group-item"><strong>ZIP Code</strong> <span
                    class="float-end">{{ $zipcode['zip_code'] }}</span></li>
            <li class="list-group-item"><strong>Delivery Rate</strong> <span
                    class="float-end">${{ $zipcode['delivery_rate'] }}</span></li>
            <li class="list-group-item"><strong>Pickup Rate</strong> <span
                    class="float-end">${{ $zipcode['pickup_rate'] }}</span></li>
        </ul>
    @endforeach
@else
    <ul class="list-group mb-3">

        <li class="list-group-item list-group-item-primary">
            <span class="badge bg-light-warning float-end">

                <a target="_blank"
                    href="{{ route('service-area.index', ['tab' => 'zipcode']) }}"><i
                        class="fas fa-external-link-alt"></i>
                    #</a>
            </span>
            Create Zipcode
        </li>
        <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
            <div>
                <p class="text-muted ms-1">Need valid zipcode</p>
            </div>
        </li>
    </ul>
@endif

