<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <link rel="stylesheet" href="style.css" media="all" />
    <style>
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
        .printBtn {
            background-color: green;
            color: white;
            border: transparent;
            padding: 7px 10px;
            border-radius: 2px;
        }
        @media print
        {    
            .no-print, .no-print *
            {
                display: none !important;
            }
        }
        a {
            color: #0087C3;
            text-decoration: none;
        }

        body {
            position: relative;
            width: 21cm;
            height: 29.7cm;
            margin: 0 auto;
            color: #555555;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-family: SourceSansPro;
        }

        header {
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #AAAAAA;
        }

        #logo {
            float: left;
            margin-top: 8px;
        }

        #logo img {
            height: 70px;
        }

        #company {
            float: right;
            text-align: right;
            width: 500px;
        }


        #details {
            margin-bottom: 50px;
        }

        #client {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
            float: left;
        }

        #client .to {
            color: #777777;
        }

        h2.name {
            font-size: 1.4em;
            font-weight: normal;
            margin: 0;
        }

        #invoice {
            float: right;
            text-align: right;
        }

        #invoice h1 {
            color: #0087C3;
            font-size: 2em;
            line-height: 1em;
            font-weight: normal;
            margin: 0 0 10px 0;
        }

        #invoice .date {
            font-size: 1.1em;
            color: #777777;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        table th,
        table td {
            padding: 20px;
            background: #EEEEEE;
            text-align: center;
            border-bottom: 1px solid #FFFFFF;
        }

        table th {
            white-space: nowrap;
            font-weight: normal;
        }

        table td {
            text-align: right;
        }

        table td h3 {
            color: #57B223;
            font-size: 1.2em;
            font-weight: normal;
            margin: 0 0 0.2em 0;
        }

        table .no {
            color: #FFFFFF;
            font-size: 1.6em;
            background: #57B223;
        }

        table .desc {
            text-align: left;
        }

        table .unit {
            background: #DDDDDD;
        }

        table .qty {}

        table .total {
            background: #57B223;
            color: #FFFFFF;
        }

        table td.unit,
        table td.qty,
        table td.total {
            font-size: 1.2em;
        }

        table tbody tr:last-child td {
            border: none;
        }

        table tfoot td {
            padding: 10px 20px;
            background: #FFFFFF;
            border-bottom: none;
            font-size: 1.2em;
            white-space: nowrap;
            border-top: 1px solid #AAAAAA;
        }

        table tfoot tr:first-child td {
            border-top: none;
        }

        table tfoot tr:last-child td {
            color: #57B223;
            font-size: 1.4em;
            border-top: 1px solid #57B223;

        }

        table tfoot tr td:first-child {
            border: none;
        }

        #thanks {
            font-size: 2em;
            margin-bottom: 50px;
        }

        #notices {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
        }

        #notices .notice {
            font-size: 1.2em;
        }

        footer {
            color: #777777;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #AAAAAA;
            padding: 8px 0;
            text-align: center;
        }
    </style>
    <script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://printjs-4de6.kxcdn.com/print.min.css">
    <script>
        function printPage() {
            window.print();
        }
    </script>
</head>
<body>
    <div class="container no-print" style="padding: 15px 0 0 0;text-align: right;">
        <button type="button" class="printBtn" onclick="printPage()">
            Print Invoice
        </button>
    </div>
    <div id="printable" class="container">
        <header class="clearfix">
                <div id="logo">
                    <img src="{{$invoice['company']['logo']}}" style="height: 100%; width: auto;" />
                </div>
                <div id="company">
                    <h2 class="name">{{$invoice['company']['company']}}</h2>
                    <div> {{$invoice['company']['address']}} </div>
                    <div>{{$invoice['company']['phone']}}</div>
                    <div>{{$invoice['company']['company_email']}}</div>
                </div>
        </div>
        </header>
        <main>
            <div id="details" class="clearfix">
                <div id="client">
                    <div class="to">INVOICE TO:</div>
                    <h2 class="name"> {{$invoice['user']['name']}} </h2>
                    <h2 class="name">Mob. {{$invoice['user']['mobile']}} </h2>
                    <!-- <div class="address">796 Silver Harbour, TX 79273, US</div> -->
                    <div class="email"><a href="mailto:john@example.com"> Mail: {{$invoice['user']['email']}} </a></div>
                </div>
                <div id="invoice">
                    <h1>INVOICE {{$invoice['invoice_no']}}</h1>
                    <div class="date">Date of Invoice: {{$invoice['invoice_date']}}</div>
                </div>
            </div>
            <table border="0" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th class="no">#</th>
                        <th class="desc">DESCRIPTION</th>
                        <th class="unit">UNIT PRICE</th>
                        <th class="qty">QUANTITY</th>
                        <th class="total">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice['data'] as $key => $data)
                    <tr>
                        <td class="no"> {{$key+1}} </td>
                        <td class="desc">
                            <h3> {{$data['title']}} </h3>
                            {{$data['description']}}
                        </td>
                        <td class="unit"> {{$invoice['pricing']['currency']['symbol']}}{{ format($data['unit_price'], 2)}} </td>
                        <td class="qty"> {{$data['quantity']}} </td>
                        <td class="total"> {{$invoice['pricing']['currency']['symbol']}}{{format($data['total'], 2)}} </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">SUBTOTAL</td>
                        <td>
                            {{ $invoice['pricing']['currency']['symbol'] }}{{ format($invoice['pricing']['subtotal'], 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">TAX</td>
                        <td>
                            {{ $invoice['pricing']['currency']['symbol'] }}{{ format($invoice['pricing']['vat'], 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">GRAND TOTAL</td>
                        <td>
                            {{ $invoice['pricing']['currency']['symbol'] }}{{ format($invoice['pricing']['total'], 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
            <div id="thanks">Thank you!</div>
            <!-- <div id="notices">
                <div>NOTICE:</div>
                <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
            </div> -->
        </main>
        <footer>
            Invoice was created on a computer and is valid without the signature and seal.
        </footer>
    </div>

</body>

</html>