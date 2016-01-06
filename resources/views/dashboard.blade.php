@extends('app')
@section('content')

    <script src="//www.google.com/jsapi"></script>
    <script>
        google.load('visualization', '1', {
            'packages': ['corechart','line']
        });

        // Load the Visualization API and the piechart package.
        google.setOnLoadCallback(function() {
            // Create our data table out of JSON data loaded from server.
            var monthInvoice = new google.visualization.DataTable('<?php echo $jsonTable_monthInvoice ?>');
            var options_monthInvoice = {
                title: '일간 매출 최근 30일',
                is3D: 'true',
                height: 300,
                colors: ['red', 'blue' ],
                focusTarget: 'category',
                legend: { position: 'none' }
            };

            // 일간 총 매출
            var chart = new google.visualization.LineChart(document.getElementById('chart_div_monthInvoice'));
            chart.draw(monthInvoice, options_monthInvoice);
        });
    </script>

    <div id="pi_top_space"></div>

    <div id="pi_dashboard">
        <div class="pi-container">
            <div class="" id="pi-summary">
                <!-- KRW -->
                <div>
                    <h1>{{ $day_totalInvoice->KRW_amount }} <span class="krw">KRW</span></h1>
                    <p>KRW 매출</p>
                </div>
                <!-- PI -->
                <div>
                    <h1>{{ $day_totalInvoice->PI_amount }} <span class="pi">PI</span></h1>
                    <p>PI 매출</p>
                </div>
                <!-- TOTAL -->
                <div>
                    <h1>{{ $day_totalInvoice->total }} <span class="krw">KRW</span></h1>
                    <p>총 매출(KRW)</p>
                </div>
            </div>

            <div id="chart_div_monthInvoice"></div>
        </div>
    </div>
@endsection