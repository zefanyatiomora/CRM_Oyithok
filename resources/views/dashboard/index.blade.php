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
      <div class="row">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="customerDoughnutChart"></canvas>
                    </div>
                </div>
                <div class="card-footer">
                    <div id="customerDoughnutLegend" class="row">
                        @foreach ($customerDoughnutLabels as $index => $label)
                            <div class="col-lg-6 col-12 mb-1">
                                <span style="display:inline-block; width:12px; height:12px; background-color:{{ $customerDoughnutColors[$index] }}; border-radius:3px; margin-right: 5px;"></span>
                                <small>Sebanyak <strong>{{ $customerDoughnutData[$index] }}</strong> Customer {{ $label }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                 <div class="card-header bg-white border-0">
                    <p class="mb-0">> Diagram dibawah adalah data uraian diketahui kebutuhannya.</p>
                    <h3 class="card-title font-weight-bold" style="color: #5C54AD;">Rating Customer</h3>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="customerLeadsBarChart"></canvas>
                    </div>
                </div>
            </div>
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
                    <div class="icon"><i class="ion ion-bag"></i></div>
                    <a href="{{ route('hold.index', ['tahun' => $tahun, 'bulan' => $bulan]) }}" 
                      class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
                    <a href="{{ route('closing.index', ['tahun' => $tahun, 'bulan' => $bulan]) }}" 
                      class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
          </div>
        </div>
        {{-- Baris BARU untuk menyejajarkan kedua chart --}}
        <div class="row mt-3">
            
            <div class="col-md-4 mb-3">
                <div class="card h-100"> {{-- h-100 untuk membuat tinggi card sama --}}
                    <div class="card-body">
                        {{-- Judul "Data Penjualan" bisa Anda tambahkan di sini jika mau --}}
                        {{-- <h4 class="card-title mb-4">Data Penjualan</h4> --}}
                        <h3 class="card-title font-weight-bold" style="color: #5C54AD;">Data Penjualan</h3>
                        <div style="height: 300px;">
                            <canvas id="penjualanChart"></canvas>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div id="penjualanChartLegend" class="row">
                            @foreach ($doughnutLabels as $index => $label)
                            <div class="col-lg-6 col-12 mb-1">
                                <span style="display:inline-block; width:12px; height:12px; background-color:{{ $doughnutColors[$index] }}; border-radius:3px; margin-right: 5px;"></span>
                                <small>Sebanyak <strong>{{ $doughnutData[$index] }}</strong> {{ $label }}</small>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card h-100"> {{-- h-100 untuk membuat tinggi card sama --}}
                    <div class="card-header bg-white border-0">
                        <p class="mb-0">> Diagram dibawah adalah perolehan data setiap produk yang telah teridentifikasi.</p>
                        <h3 class="card-title font-weight-bold" style="color: #5C54AD;">Data per-Produk</h3>
                    </div>
                    <div class="card-body">
                        <div style="height: 350px;">
                            <canvas id="produkChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
            <div class="card h-100">
                 <div class="card-header bg-white border-0">
                    <h3 class="card-title font-weight-bold" style="color: #5C54AD;">Rate Customer Closing</h3>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="rateClosingLineChart"></canvas>
                    </div>
                </div>
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
{{-- <script src="{{ asset('adminlte/plugins/chart.js/Chart.min.js') }}"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(function () {
    // --- 1. Doughnut Chart (Data Customer) ---
            const customerDoughnutCanvas = document.getElementById('customerDoughnutChart');
            if (customerDoughnutCanvas) {
                new Chart(customerDoughnutCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($customerDoughnutLabels) !!},
                        datasets: [{
                            data: {!! json_encode($customerDoughnutData) !!},
                            backgroundColor: {!! json_encode($customerDoughnutColors) !!},
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            datalabels: {
                                formatter: (value, ctx) => {
                                    const sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = (value / sum * 100).toFixed(1) + '%';
                                    return percentage;
                                },
                                color: '#000',
                                font: { weight: 'bold' }
                            },
                            legend: { display: false },
                            title: {
                                display: true,
                                text: 'Data Customer',
                                font: { size: 20 },
                                align: 'start'
                            }
                        }
                    }
                });
            }
            // --- BARU: Bar Chart (Rating Customer - Leads Baru vs Lama) ---
            const customerLeadsBarCanvas = document.getElementById('customerLeadsBarChart');
            if (customerLeadsBarCanvas) {
                new Chart(customerLeadsBarCanvas, {
                    type: 'bar',
                    data: {
                        labels: ['Baru', 'Lama'],
                        datasets: [{
                            label: 'Jumlah Customer',
                            data: [{{ $totalLeadsBaru }}, {{ $totalLeadsLama }}], // Data dari Controller
                            backgroundColor: ['#6C63AC', '#FF7373'],
                            borderRadius: 8,
                            barPercentage: 0.6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { 
                                    precision: 0, // Hanya angka bulat
                                    stepSize: 1 // Kelipatan 2
                                }
                            }
                        },
                        plugins: {
                            legend: { 
                                display: false // Sembunyikan legenda
                            }
                        }
                    }
                });
            }
            // ... di dalam $(function () { ... }); ...

            // --- BARU: Line Chart (Rate Customer Closing) ---
            const rateClosingCanvas = document.getElementById('rateClosingLineChart');
            if (rateClosingCanvas) {
                new Chart(rateClosingCanvas, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($rateClosingLabels) !!},
                        // Dataset diambil langsung dari controller, sudah lengkap dengan style
                        datasets: {!! json_encode($rateClosingDatasets) !!}
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 }
                            }
                        },
                        plugins: {
                            tooltip: {
                                enabled: true, // Pastikan ini 'true' atau hapus baris ini (karena default-nya sudah true)
                                
                                // --- OPSI TAMBAHAN (SANGAT DIREKOMENDASIKAN) ---
                                mode: 'index',
                                intersect: false,
                            },
                            legend: {
                                position: 'top',
                                labels: {
                                    // TAMBAHKAN BARIS INI
                                    // ===================================
                                    usePointStyle: true,
                                    // ===================================
                                    // Mengganti label default (Minggu 1) menjadi angka (1)
                                    generateLabels: function(chart) {
                                        const originalLabels = Chart.defaults.plugins.legend.labels.generateLabels(chart);
                                        originalLabels.forEach(label => {
                                            label.text = label.text.replace('Minggu ', '');
                                        });
                                        return originalLabels;
                                    }
                                }
                            }
                        }
                    }
                });
            }

   // ======================================================
    // LOGIKA UNTUK SEMUA CHART DI DALAM TAB "PRODUK"
    // ======================================================
    
    // Gunakan satu flag untuk menandai apakah chart di tab ini sudah dirender
    let produkTabChartsRendered = false;

    // Buat satu fungsi untuk merender SEMUA chart di dalam Tab Produk
    function renderProdukTabCharts() {
        // --- 1. Render Doughnut Chart Penjualan ---
        const penjualanCanvas = document.getElementById('penjualanChart');
        if (penjualanCanvas) {
            new Chart(penjualanCanvas, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($doughnutLabels) !!},
                    datasets: [{
                        data: {!! json_encode($doughnutData) !!},
                        backgroundColor: {!! json_encode($doughnutColors) !!},
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: {
                            formatter: (value, ctx) => {
                                const sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = (value / sum * 100).toFixed(1) + '%';
                                return percentage;
                            },
                            color: '#fff',
                            font: { weight: 'bold', size: 14 }
                        },
                        legend: { display: false },
                        // title: {
                        //     display: true,
                        //     text: 'Data Penjualan',
                        //     font: { size: 20 },
                        //     padding: { bottom: 20 }
                        // }
                    }
                }
            });
        }

        // --- 2. Render Bar Chart Produk ---
        const produkCanvas = document.getElementById('produkChart');
        if (produkCanvas) {
            new Chart(produkCanvas, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($kategoriLabels) !!},
                    datasets: [{
                        label: 'Ask',
                        data: {!! json_encode($dataAsk) !!},
                        backgroundColor: 'rgba(122, 160, 255, 0.8)',
                    }, {
                        label: 'Hold',
                        data: {!! json_encode($dataHold) !!},
                        backgroundColor: 'rgba(74, 85, 162, 0.8)',
                    }, {
                        label: 'Closing',
                        data: {!! json_encode($dataClosing) !!},
                        backgroundColor: 'rgba(239, 87, 119, 0.8)',
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
                    plugins: { legend: { position: 'top' } }
                }
            });
        }
    }

    // Event listener yang HANYA memanggil satu fungsi render di atas
    $('a[data-toggle="tab"][href="#produk"]').on('shown.bs.tab', function (e) {
        if (!produkTabChartsRendered) {
            renderProdukTabCharts();
            produkTabChartsRendered = true; // Set flag agar tidak render ulang
        }
    });

    // Cek jika tab "Produk" aktif saat halaman pertama kali dimuat
    if ($('#produk').hasClass('show active')) {
        renderProdukTabCharts();
        produkTabChartsRendered = true;
    }

});
</script>
@endpush
