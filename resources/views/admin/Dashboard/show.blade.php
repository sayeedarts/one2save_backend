@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            @include('admin.Dashboard._dashlets')
            @include('admin.Dashboard._statistics')
            @include('admin.Dashboard._details_table')
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.6.2/dist/chart.min.js"></script>
<script>
    let xAxis = JSON.parse('<?php echo $chart_data['days'] ?>')
    let storage = JSON.parse('<?php echo $chart_data['storage'] ?>')
    let packing = JSON.parse('<?php echo $chart_data['packing'] ?>')

    const ctx = document.getElementById('myChart');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: xAxis,
            datasets: [{
                    label: 'Storage Orders',
                    data: storage,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                    ],
                    borderWidth: 1
                },
                {
                    label: 'Packing Orders',
                    data: packing,
                    backgroundColor: [
                        'rgba(251, 227, 13, 1)',
                        'rgba(230, 24, 24, 0.5)',
                    ],
                    borderColor: [
                        'rgba(239, 217, 26, 1)',
                        'rgba(230, 24, 24, 1)',
                    ],
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<script src="{{ asset( path('admin') . '/js/apexcharts.min.js') }}"></script>
@endsection