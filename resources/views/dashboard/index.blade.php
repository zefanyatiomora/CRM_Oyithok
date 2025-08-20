@extends('layouts.template')

@section('content')

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Filter Tahun & Bulan -->
        <div class="row mb-3">
            <div class="col-md-8">
                <form method="GET" action="{{ route('dashboard') }}" class="form-inline">
                    <select name="tahun" class="form-control mr-2" required>
                        <option value="">-- Pilih Tahun --</option>
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $year == $tahun ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>

                    <select name="bulan" class="form-control mr-2">
                        <option value="">-- Semua Bulan --</option>
                        @foreach($bulanList as $key => $label)
                            <option value="{{ $key }}" {{ $key == $bulan ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    <div class="btn-group mr-2">
                        <button type="submit" class="btn btn-info">Filter</button>
                        <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('dashboard') }}">Reset Filter</a>
                            <a class="dropdown-item" href="#">Cetak Laporan</a>
                            <a class="dropdown-item" href="#">Export Excel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kotak Statistik -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $jumlahInteraksi }}</h3>
                        <p>Jumlah Interaksi
                            @if ($bulan)
                            ({{ $bulanList[$bulan] }} {{ $tahun }})
                            @else
                            (Tahun {{ $tahun }})
                            @endif
                        </p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <a href="{{ route('rekap.index', ['tahun' => $tahun, 'bulan' => $bulan]) }}" 
                    class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- prosesSSurvey -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $prosesSurvey }}</h3>
                        <p>Proses Survey
                            @if ($bulan)
                            ({{ $bulanList[$bulan] }} {{ $tahun }})
                            @else
                            (Tahun {{ $tahun }})
                            @endif
                        </p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-search-location"></i>
                    </div>
                    <a href="{{ route('survey.index', ['tahun' => $tahun, 'bulan' => $bulan, 'status' => 'survey']) }}" 
                    class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $prosesPasang }}</h3>
                        <p>Proses Pasang
                            @if ($bulan)
                            ({{ $bulanList[$bulan] }} {{ $tahun }})
                            @else
                            (Tahun {{ $tahun }})
                            @endif
                        </p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ route('pasang.index', ['tahun' => $tahun, 'bulan' => $bulan, 'status' => 'survey']) }}" 
                    class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- ... kotak lainnya -->

            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                  <h3>44</h3>
                  
                  <p>User Registrations</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>65</h3>
                    
                    <p>Unique Visitors</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>
    <!-- /.row -->
    <!-- Main row -->
    <div class="row">
        <!-- Left col -->
        <section class="col-lg-7 connectedSortable">
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Sales
                    </h3>
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a class="nav-link active" href="#bar-chart" data-toggle="tab">Area</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#sales-chart" data-toggle="tab">Donut</a>
                            </li>
                        </ul>
                    </div>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content p-0">
                        <!-- Morris chart - Sales -->
                        <div class="chart tab-pane active" id="bar-chart"
                            style="position: relative; height: 300px;">
                            {{-- Tempatkan canvas chart --}}
                            <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                        <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;">
                            <canvas id="sales-chart-canvas" height="300" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div><!-- /.card-body -->
            </div>
        </section>
    </div>
</div><!-- /.container-fluid -->

@endsection
@push('js')
<script src="{{ asset('adminlte/plugins/chart.js/Chart.min.js') }}"></script>
<script>
    $(function () {
        // Contoh data dummy, bisa kamu ganti dari Controller / Ajax
        var areaChartData = {
            labels  : ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: [
                {
                    label               : 'Leads Lama',
                    backgroundColor     : 'rgba(60,141,188,0.9)',
                    borderColor         : 'rgba(60,141,188,0.8)',
                    pointRadius         : false,
                    pointColor          : '#3b8bba',
                    pointStrokeColor    : 'rgba(60,141,188,1)',
                    pointHighlightFill  : '#fff',
                    pointHighlightStroke: 'rgba(60,141,188,1)',
                    data                : [28, 48, 40, 19, 86, 27, 90]
                },
                {
                    label               : 'Leads Baru',
                    backgroundColor     : 'rgba(210, 214, 222, 1)',
                    borderColor         : 'rgba(210, 214, 222, 1)',
                    pointRadius         : false,
                    pointColor          : 'rgba(210, 214, 222, 1)',
                    pointStrokeColor    : '#c1c7d1',
                    pointHighlightFill  : '#fff',
                    pointHighlightStroke: 'rgba(220,220,220,1)',
                    data                : [65, 59, 80, 81, 56, 55, 40]
                }
            ]
        }
        
        // Swap dataset (seperti contohmu)
        var barChartData = $.extend(true, {}, areaChartData)
        var temp0 = areaChartData.datasets[0]
        var temp1 = areaChartData.datasets[1]
        barChartData.datasets[0] = temp1
        barChartData.datasets[1] = temp0
        
        var barChartCanvas = $('#barChart').get(0).getContext('2d')
        var barChartOptions = {
            responsive              : true,
            maintainAspectRatio     : false,
            datasetFill             : false
        }
        
        new Chart(barChartCanvas, {
            type: 'bar',
            data: barChartData,
            options: barChartOptions
        })
    })
</script>
@endpush