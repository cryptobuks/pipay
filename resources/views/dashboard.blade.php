@extends('app')
@section('content')
<script src="//www.google.com/jsapi"></script>
    <script>
        Architekt.event.on('ready', function() {

        });

        // Load the Visualization API and the piechart package.
        google.load('visualization', '1', {'packages':['corechart','line']});

        // Set a callback to run when the Google Visualization API is loaded.
        google.setOnLoadCallback(drawChart);

        function chartOption(title) {
            options = {
                title: title,
                is3D: 'true',
                width: 1200,
                height: 300,
                colors: ['red', 'blue' ],
                focusTarget: 'category',
                legend: { position: 'none' }
            };

            return options
        }

        function drawChart() {

            // Create our data table out of JSON data loaded from server.
            var monthInvoice = new google.visualization.DataTable('<?php echo $jsonTable_monthInvoice ?>');
            var options_monthInvoice = chartOption('일간 매출 최근 30일');
            // var options_day_DW = chartOption("{{ trans('pages.admin.dashboard.dayDepositWithdraw') }}");

            // 일간 총 매출
            var chart = new google.visualization.LineChart(document.getElementById('chart_div_monthInvoice'));
            chart.draw(monthInvoice, options_monthInvoice);
        }
    </script>

    <div id="pi_top_space"></div>

    <div id="pi_dashboard">
        <div class="pi-container">
            <h1>Dashboard</h1>

            {{ $day_totalInvoice->KRW_amount }}
            {{ $day_totalInvoice->PI_amount }}
            {{ $day_totalInvoice->total }}

            <div id="chart_div_monthInvoice" style="width: 50%; min-height: 300px; display: inline-block; float: left; vertical-align: top; position: relative;"></div>
        </div>
    </div>
@endsection