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

        <!-- JUMLAH INTERAKSI -->
        <div class="row">
            <div class="col-12">
                <a href="{{ route('rekap.index', ['tahun' => $tahun, 'bulan' => $bulan]) }}" class="text-decoration-none text-white">
                    <div class="small-box bg-success box-hover">
                        <div class="inner text-center">
                            <h3>{{ $jumlahInteraksi }}</h3>
                            <p class="fw-bold">
                                JUMLAH INTERAKSI
                                @if ($bulan)
                                    ({{ $bulanList[$bulan] }} {{ $tahun }})
                                @else
                                    TAHUN {{ $tahun }}
                                @endif
                            </p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Tabs Customer & Produk -->
<ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="customer-tab" data-toggle="tab" href="#customer" role="tab">Customer</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="produk-tab" data-toggle="tab" href="#produk" role="tab">Produk</a>
  </li>
</ul>

<div class="tab-content mt-3">
  <!-- TAB CUSTOMER -->
  <div class="tab-pane fade show active" id="customer" role="tabpanel">
      <div class="row">
          <!-- STATUS ASK -->
          <div class="col-lg-3 col-6">
              <a href="{{ route('dashboard.ask', ['tahun' => $tahun, 'bulan' => $bulan, 'status' => 'survey']) }}" 
                 class="text-decoration-none text-white">
                  <div class="small-box bg-info box-hover">
                      <div class="inner text-center">
                          <h3>{{ $jumlahAsk }}</h3>
                          <p>ASK 
                              @if ($bulan) ({{ $bulanList[$bulan] }} {{ $tahun }}) 
                              @else TAHUN {{ $tahun }} 
                              @endif
                          </p>
                      </div>
                      <div class="icon"><i class="ion ion-stats-bars"></i></div>
                  </div>
              </a>
          </div>

          <!-- STATUS FOLLOW UP -->
          <div class="col-lg-3 col-6">
              <a href="{{ route('dashboard.followup', ['tahun' => $tahun, 'bulan' => $bulan, 'status' => 'survey']) }}" 
                 class="text-decoration-none text-white">
                  <div class="small-box bg-primary box-hover">
                      <div class="inner text-center">
                          <h3>{{ $jumlahFollowUp }}</h3>
                          <p>FOLLOW UP 
                              @if ($bulan) ({{ $bulanList[$bulan] }} {{ $tahun }}) 
                              @else TAHUN {{ $tahun }} 
                              @endif
                          </p>
                      </div>
                      <div class="icon"><i class="ion ion-stats-bars"></i></div>
                  </div>
              </a>
          </div>

          <!-- STATUS HOLD -->
          <div class="col-lg-3 col-6">
              <a href="{{ route('dashboard.hold', ['tahun' => $tahun, 'bulan' => $bulan, 'status' => 'survey']) }}" 
                 class="text-decoration-none text-white">
                  <div class="small-box bg-navy box-hover">
                      <div class="inner text-center">
                          <h3>{{ $jumlahHold }}</h3>
                          <p>HOLD 
                              @if ($bulan) ({{ $bulanList[$bulan] }} {{ $tahun }}) 
                              @else TAHUN {{ $tahun }} 
                              @endif
                          </p>
                      </div>
                      <div class="icon"><i class="ion ion-stats-bars"></i></div>
                  </div>
              </a>
          </div>

          <!-- STATUS CLOSING -->
          <div class="col-lg-3 col-6">
              <a href="{{ route('dashboard.closing', ['tahun' => $tahun, 'bulan' => $bulan, 'status' => 'survey']) }}" 
                 class="text-decoration-none text-white">
                  <div class="small-box bg-danger box-hover">
                      <div class="inner text-center">
                          <h3>{{ $jumlahClosing }}</h3>
                          <p>CLOSING 
                              @if ($bulan) ({{ $bulanList[$bulan] }} {{ $tahun }}) 
                              @else TAHUN {{ $tahun }} 
                              @endif
                          </p>
                      </div>
                      <div class="icon"><i class="ion ion-stats-bars"></i></div>
                  </div>
              </a>
          </div>
      </div>
      <!-- Chart Leads -->
    <div class="card mt-3">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">Diagram Leads</h3>
        </div>
        <div class="card-body">
            {{-- <canvas id="leadsChart" style="height:300px;"></canvas> --}}
            {{-- Tempatkan canvas chart --}}
            <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
        </div>
    </div>
  </div>

  <!-- TAB PRODUK -->
  <div class="tab-pane fade" id="produk" role="tabpanel">
      <div class="row">
          <!-- STATUS ASK PRODUK -->
          <div class="col-lg-4 col-6">
              <div class="small-box bg-info box-hover">
                  <div class="inner text-center">
                      <h3>{{ $jumlahProdukAsk ?? 0 }}</h3>
                      <p>ASK PRODUK 
                          @if ($bulan) ({{ $bulanList[$bulan] }} {{ $tahun }}) 
                          @else TAHUN {{ $tahun }} 
                          @endif
                      </p>
                  </div>
                  <div class="icon"><i class="ion ion-bag"></i></div>
                  <a href="{{ route('ask.index', ['tahun' => $tahun, 'bulan' => $bulan]) }}" 
                    class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
          </div>

          <!-- STATUS HOLD PRODUK -->
          <div class="col-lg-4 col-6">
              <div class="small-box bg-navy box-hover">
                  <div class="inner text-center">
                      <h3>{{ $jumlahProdukHold ?? 0 }}</h3>
                      <p>HOLD PRODUK 
                          @if ($bulan) ({{ $bulanList[$bulan] }} {{ $tahun }}) 
                          @else TAHUN {{ $tahun }} 
                          @endif
                      </p>
                  </div>
                  <div class="icon"><i class="ion ion-bag"></i></div>
              </div>
          </div>

          <!-- STATUS CLOSING PRODUK -->
          <div class="col-lg-4 col-6">
              <div class="small-box bg-danger box-hover">
                  <div class="inner text-center">
                      <h3>{{ $jumlahProdukClosing ?? 0 }}</h3>
                      <p>CLOSING PRODUK 
                          @if ($bulan) ({{ $bulanList[$bulan] }} {{ $tahun }}) 
                          @else TAHUN {{ $tahun }} 
                          @endif
                      </p>
                  </div>
                  <div class="icon"><i class="ion ion-bag"></i></div>
              </div>
          </div>
      </div>
    <!-- Chart Produk -->
    <div class="card mt-3">
        <div class="card-header bg-danger text-white">
            <h3 class="card-title">Diagram Produk</h3>
        </div>
        <div class="card-body">
            <canvas id="produkChart" style="height:300px;"></canvas>
        </div>
    </div>
  </div>
</div>
</section>

@push('css')
<style>
    .box-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .box-hover:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    }
</style>
@endpush
@endsection
@push('js')
<script src="{{ asset('adminlte/plugins/chart.js/Chart.min.js') }}"></script>
<script>
    $(function () {
        // Ganti data dummy dengan data dinamis dari Controller
        var areaChartData = {
            // Gunakan data label dari controller
            labels  : {!! json_encode($chartLabels) !!},
            datasets: [
                {
                    label               : 'Leads Baru',
                    backgroundColor     : 'rgba(60,141,188,0.9)',
                    borderColor         : 'rgba(60,141,188,0.8)',
                    pointRadius         : false,
                    pointColor          : '#3b8bba',
                    pointStrokeColor    : 'rgba(60,141,188,1)',
                    pointHighlightFill  : '#fff',
                    pointHighlightStroke: 'rgba(60,141,188,1)',
                    // Gunakan data leads lama dari controller
                    data                : {!! json_encode($dataLeadsLama) !!}
                },
                {
                    label               : 'Leads Lama',
                    backgroundColor     : 'rgba(210, 214, 222, 1)',
                    borderColor         : 'rgba(210, 214, 222, 1)',
                    pointRadius         : false,
                    pointColor          : 'rgba(210, 214, 222, 1)',
                    pointStrokeColor    : '#c1c7d1',
                    pointHighlightFill  : '#fff',
                    pointHighlightStroke: 'rgba(220,220,220,1)',
                    // Gunakan data leads baru dari controller
                    data                : {!! json_encode($dataLeadsBaru) !!}
                }
            ]
        }
        
        // Kode di bawah ini tidak perlu diubah
        var barChartData = $.extend(true, {}, areaChartData)
        var temp0 = areaChartData.datasets[0]
        var temp1 = areaChartData.datasets[1]
        barChartData.datasets[0] = temp1
        barChartData.datasets[1] = temp0
        
        var barChartCanvas = $('#barChart').get(0).getContext('2d')
        // --- MODIFIKASI DI SINI ---
        var barChartOptions = {
            responsive              : true,
            maintainAspectRatio     : false,
            datasetFill             : false,
            // Tambahkan konfigurasi scales berikut:
            scales: {
                y: { // Konfigurasi untuk sumbu Y
                    ticks: {
                        // Memastikan sumbu Y dimulai dari angka 0
                        beginAtZero: true,
                        // Memaksa langkah antar-label menjadi kelipatan 1 (menghilangkan desimal)
                        stepSize: 2,
                        // Opsi tambahan untuk memastikan tidak ada desimal jika stepSize tidak cukup
                        precision: 0
                    }
                }
            }
        }
        
        // --- AKHIR MODIFIKASI ---
        
        new Chart(barChartCanvas, {
            type: 'bar',
            data: barChartData,
            options: barChartOptions // Gunakan options yang sudah dimodifikasi
        })

        // ProdukChart 
        // Ambil elemen canvas dari HTML
        const ctx = document.getElementById('produkChart');

        // Ambil data dari controller menggunakan json_encode()
        const kategoriLabels = {!! json_encode($kategoriLabels) !!};
        const dataAsk = {!! json_encode($dataAsk) !!};
        const dataHold = {!! json_encode($dataHold) !!};
        const dataClosing = {!! json_encode($dataClosing) !!};

        // Konfigurasi dan pembuatan chart (bagian ini tetap sama)
        new Chart(ctx, {
            type: 'bar', // Tipe chart adalah bar chart
            data: {
                labels: kategoriLabels, // Label untuk sumbu Y (nama-nama produk)
                datasets: [{
                    label: 'Ask',
                    data: dataAsk,
                    backgroundColor: 'rgba(122, 160, 255, 0.8)', // Biru muda
                    borderColor: 'rgba(122, 160, 255, 1)',
                    borderWidth: 1
                }, {
                    label: 'Hold',
                    data: dataHold,
                    backgroundColor: 'rgba(74, 85, 162, 0.8)',  // Biru tua
                    borderColor: 'rgba(74, 85, 162, 1)',
                    borderWidth: 1
                }, {
                    label: 'Closing',
                    data: dataClosing,
                    backgroundColor: 'rgba(239, 87, 119, 0.8)', // Merah/Pink
                    borderColor: 'rgba(239, 87, 119, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y', // **Ini kunci untuk membuat bar chart menjadi HORIZONTAL**
                responsive: true, // Membuat chart menyesuaikan ukuran container
                maintainAspectRatio: false, // Penting agar chart bisa mengisi tinggi container
                scales: {
                    x: {
                        beginAtZero: true, // Sumbu X (angka) dimulai dari 0
                        ticks: {
                            // Memastikan hanya angka bulat (integer) yang ditampilkan di sumbu X
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top', // Posisi legenda di atas chart
                    },
                    title: {
                        display: false,
                        text: 'Data per-Produk'
                    }
                }
            }
        });
    })
</script>
@endpush
