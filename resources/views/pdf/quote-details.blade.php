<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529
        }

        .table td,
        .table th {
            padding: .75rem;
            vertical-align: middle;
            border-top: 1px solid #dee2e6
        }

        .table thead th {
            vertical-align: middle;
            border-bottom: 2px solid #dee2e6
        }

        .table tbody+tbody {
            border-top: 2px solid #dee2e6
        }

        .table-sm td,
        .table-sm th {
            padding: .3rem
        }
        .badge {
            padding: 2px 10px;
            border-radius: 10px;
        }
        .badge.badge-default {
            background-color: yellow;
        }
        .badge.badge-success {
            background-color: #4caf50;
            color: #ffffff;
        }
        .badge.badge-danger {
            background-color: #f44336;
            color: #ffffff;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, .05)
        }
    </style>
</head>

<body>
    {!! $template_data !!}
    <div>
        <h3>Pickup Location Details</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>From Location:</th>
                    <th>To Location: </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div> {{$quote_details['data']['from_location']}} </div>
                        @if (!empty($quote_details['data']['floor_details']['from_floor']))
                        <div>
                            From floor: {{$quote_details['data']['floor_details']['from_floor']}}
                            @if (!empty($quote_details['data']['floor_details']['from_lift'])) 
                                <small class="badge badge-success">Yes, It has Lift facility.</small>
                            @else 
                                <small class="badge badge-danger">No, It has no Lift facility.</small>
                            @endif
                        </div>
                        @endif
                    </td>
                    <td>
                        <div> {{$quote_details['data']['to_location']}} </div>
                        @if (!empty($quote_details['data']['floor_details']['to_floor']))
                        <div>
                            From floor: {{$quote_details['data']['floor_details']['to_floor']}}
                            @if (!empty($quote_details['data']['floor_details']['to_lift'])) 
                                <small class="badge badge-success">Yes, It has Lift facility.</small>
                            @else 
                                <small class="badge badge-danger">No, It has no Lift facility.</small>
                            @endif
                        </div>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- <h3>Pickup Location Details</h3>
        <p>From Location: {{$quote_details['data']['from_location']}} </p>
        <p>To Location: {{$quote_details['data']['to_location']}} </p> -->

        <h3>User Details ({{$quote_details['data']['user_type']}})</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Full Name:</th>
                    <th>Email: </th>
                    <th>Mobile: </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>{{$quote_details['data']['fullname']}}</strong></td>
                    <td>{{$quote_details['data']['email']}}</td>
                    <td>{{$quote_details['data']['mobile']}}</td>
                </tr>
            </tbody>
        </table>

        <h3>Selected Service </h3>
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>Selected Service</th>
                    <th>Category Name</th>
                    <th>Item Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote_details['data']['service']['category'] as $key => $category)
                <tr>
                    @if ($key == 0)
                    <td rowspan="{{count($quote_details['data']['service']['category'])}}">
                        {{$quote_details['data']['service']['name']}}
                    </td>
                    @endif
                    <td>
                        {{$category['name']}}
                    </td>
                    <td>
                        <ul>
                            @foreach($category['items'] as $item)
                            <li>
                                {{$item['item']['name']}} (Qty: x{{$item['item']['count']}})
                            </li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div>
        <h3>Custom Service Items:</h3>
        <p>{{$quote_details['data']['custom_item_list']}}</p>
    </div>
    <div>

        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th align="left">
                        Additional Services
                    </th>
                    <th align="left">
                        Pickup Details
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                            @foreach($quote_details['data']['additional_services'] as $key => $additionalSvc)
                            <div>
                                <div> {{$key+1}}. {{$additionalSvc['name']}}</div>
                                <div>
                                    @if (!empty($additionalSvc['extra']) && !empty($additionalSvc['type']))
                                        <small class="badge badge-default">
                                            @if ($additionalSvc['type'] === 'porter')
                                                Total porter(s) asked: 
                                            @elseif ($additionalSvc['type'] === 'storage')
                                                Total week(s) asked: 
                                            @endif
                                            {{$additionalSvc['extra']}}
                                        </small>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </ul>
                    </td>
                    <td>
                        <div>
                            <p>Pickup type: {{ $quote_details['data']['pickup_details']['name'] }}</p>
                            <p>Pickup Date: {{ $quote_details['data']['pickup_details']['date'] }}</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div>
        <h3>Special Instructions</h3>
        <p>
            {{$quote_details['data']['instruction']}}
        </p>
    </div>
</body>

</html>