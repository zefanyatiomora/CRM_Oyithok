@extends('layouts.template')

@section('content')

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
<!-- Filter Tahun & Bulan -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('dashboard') }}">
            <div class="form-row align-items-end">
                <!-- Pilih Tahun -->
                <div class="col-md-3 mb-3">
                    <label for="tahun" class="small text-muted font-weight-bold">
    Tahun
    <i class="fas fa-question-circle text-secondary ml-1" 
       data-bs-toggle="tooltip" 
       title="Pilih tahun untuk menampilkan data dashboard"></i>
</label>
                    <select name="tahun" id="tahun" class="form-control rounded-pill shadow-sm" required>
                        <option value="">-- Pilih Tahun --</option>
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $year == $tahun ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pilih Bulan -->
                <div class="col-md-3 mb-3">
                    <label for="bulan" class="small text-muted font-weight-bold">
    Bulan
    <i class="fas fa-question-circle text-secondary ml-1" 
       data-bs-toggle="tooltip" 
       title="Pilih bulan untuk menampilkan data dashboard (opsional)"></i>
</label>
                    <select name="bulan" id="bulan" class="form-control rounded-pill shadow-sm">
                        <option value="">-- Semua Bulan --</option>
                        @foreach($bulanList as $key => $label)
                            <option value="{{ $key }}" {{ $key == $bulan ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Aksi -->
                <div class="col-md-4 mb-3">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="fas fa-search mr-1"></i> Filter
                        </button>
                        <button type="button" 
                                class="btn btn-outline-primary rounded-pill px-3 shadow-sm dropdown-toggle dropdown-toggle-split" 
                                data-toggle="dropdown">
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="{{ route('dashboard') }}">
                                <i class="fas fa-undo mr-1"></i> Reset Filter
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-print mr-1"></i> Cetak Laporan
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-file-excel mr-1"></i> Export Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

       <!-- JUMLAH INTERAKSI -->
<div class="row">
    <div class="col-12">
        <a href="{{ route('rekap.index', ['tahun' => $tahun, 'bulan' => $bulan]) }}" 
           class="text-decoration-none text-white"
           data-bs-toggle="tooltip"
           title="Total interaksi customer untuk bulan/tahun yang dipilih">
            <div class="small-box bg-success box-hover">
                <div class="inner text-center">
                    <h3>{{ $jumlahInteraksi }}</h3>
                    <p class="fw-bold">
                        INTERAKSI CUSTOMER
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
<div class="status-boxes">
    <!-- STATUS GHOST -->
    <a href="{{ route('dashboard.ghost') }}" 
       class="text-decoration-none text-white"
       data-bs-toggle="tooltip" 
       title="Customer yang sudah diketahui kebutuhannya, tetapi tidak jadi pesan">
        <div class="small-box bg-custom-ghost box-hover">
            <div class="inner text-center">
                <h3>{{ $jumlahGhost }}</h3>
                <p>GHOST (SEMUA DATA)</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-secret"></i>
            </div>
        </div>
    </a>

    <!-- STATUS ASK -->
    <a href="{{ route('dashboard.ask', ['status' => 'ask']) }}" 
       class="text-decoration-none text-white"
       data-bs-toggle="tooltip" 
       title="Customer yang sudah diketahui kebutuhannya tetapi hilang/tidak ada kelanjutan.">
        <div class="small-box bg-custom-ask box-hover">
            <div class="inner text-center">
                <h3>{{ $jumlahAsk }}</h3>
                <p>ASK (SEMUA DATA)</p>
            </div>
            <div class="icon">
                <i class="fas fa-question-circle"></i>
            </div>
        </div>
    </a>

    <!-- STATUS FOLLOW UP -->
    <a href="{{ route('dashboard.followup') }}" 
       class="text-decoration-none text-white"
       data-bs-toggle="tooltip" 
       title="Customer yang perlu ditindaklanjuti untuk dipastikan jadi pesan atau tidak.">
        <div class="small-box bg-custom-follow-up box-hover">
            <div class="inner text-center">
                <h3>{{ $jumlahFollowUp }}</h3>
                <p>FOLLOW UP (SEMUA DATA)</p>
            </div>
            <div class="icon">
                <i class="fas fa-comments"></i>
            </div>
        </div>
    </a>

    <!-- STATUS HOLD -->
    <a href="{{ route('dashboard.hold') }}" 
       class="text-decoration-none text-white"
       data-bs-toggle="tooltip" 
       title="Customer yang menjanjikan pemesanan di lain waktu.">
        <div class="small-box bg-custom-hold box-hover">
            <div class="inner text-center">
                <h3>{{ $jumlahHold }}</h3>
                <p>HOLD (SEMUA DATA)</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </a>

    <!-- STATUS CLOSING -->
    <a href="{{ route('dashboard.closing', ['status' => 'survey']) }}" 
       class="text-decoration-none text-white"
       data-bs-toggle="tooltip" 
       title="Customer yang telah selesai pemesanan (pemasangan/pengiriman).">
        <div class="small-box bg-custom-closing box-hover">
            <div class="inner text-center">
                <h3>{{ $jumlahClosing }}</h3>
                <p>CLOSING (SEMUA DATA)</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </a>
</div>
      </div>
<div class="row">
    <!-- Card Doughnut -->
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="customerDoughnutChart"></canvas>
                </div>
            </div>
            <div class="card-footer">
                <div id="customerDoughnutLegend" class="row">
                    @php
                        $statuses = [
                            'Ghost' => '#9A9D9E',      // Abu-abu
                            'Ask' => '#87CEEB',        // Biru toska
                            'Hold' => '#5C54AD',       // Ungu
                            'Follow Up' => '#A374FF',  // Ungu muda
                            'Closing' => '#FF7373',    // Merah
                        ];
                    @endphp

                    @foreach ($statuses as $label => $color)
                        <div class="col-lg-6 col-12 mb-1">
                            <span style="display:inline-block; width:12px; height:12px; background-color:{{ $color }}; border-radius:3px; margin-right: 5px;"></span>
                            <small>Kategori {{ $label }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Card Bar + Line Chart -->
<div class="col-md-8 mb-3">
    <p class="text-muted mb-2">
        > Diagram dibawah adalah data uraian setiap tipe customer yang masuk dan telah diketahui kebutuhannya.
    </p>
    <div class="card">
            <div class="card-body">
                <div class="row">
                    <!-- Grafik Rating Customer -->
                    <div class="col-md-6 mb-3">
                        <h5 class="font-weight-bold" style="color: #5C54AD;">Rating Customer</h5>
                        <div style="height: 300px;">
                            <canvas id="customerLeadsBarChart"></canvas>
                        </div>
                    </div>

                    <!-- Grafik Rate Customer Closing -->
                    <div class="col-md-6 mb-3">
                        <h5 class="font-weight-bold" style="color: #5C54AD;">
                            Rate Customer Closing
                        <div style="height: 300px;">
                            <canvas id="rateClosingLineChart"></canvas>
                        </div>
                    </div>
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
    <a href="{{ route('ask.index') }}" 
       class="small-box bg-custom-ask box-hover text-decoration-none text-dark"
       data-bs-toggle="tooltip" 
       title="Total ASK produk yang tersedia">
        <div class="inner text-center">
            <h3>{{ $jumlahProdukAsk ?? 0 }}</h3>
            <p>ASK PRODUK</p>
        </div>
        <div class="icon"><i class="fas fa-question-circle"></i></div>
    </a>
</div>

<!-- STATUS HOLD PRODUK -->
<div class="col-lg-4 col-6">
    <a href="{{ route('hold.index') }}" 
       class="small-box bg-custom-hold box-hover text-decoration-none text-dark"
       data-bs-toggle="tooltip" 
       title="Total HOLD produk (semua)">
        <div class="inner text-center">
            <h3>{{ $jumlahProdukHold ?? 0 }}</h3>
            <p>HOLD PRODUK (SEMUA)</p>
        </div>
        <div class="icon"><i class="fas fa-pause-circle"></i></div>
    </a>
</div>

<!-- STATUS CLOSING PRODUK -->
<div class="col-lg-4 col-6">
    <a href="{{ route('closing.index') }}" 
       class="small-box bg-danger box-hover text-decoration-none text-dark"
       data-bs-toggle="tooltip" 
       title="Total CLOSING produk (semua)">
        <div class="inner text-center">
            <h3>{{ $jumlahProdukClosing ?? 0 }}</h3>
            <p>CLOSING PRODUK (SEMUA)</p>
        </div>
        <div class="icon"><i class="fas fa-pause-circle"></i></div>
    </a>
</div>

@push('js')
<script>
$(document).ready(function () {
    // Saat tab diklik → update hash di URL (#customer atau #produk)
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr("href"); // #customer / #produk
        history.replaceState(null, null, target); 
    });

    // Saat halaman dimuat → cek hash di URL, buka tab sesuai hash
    const hash = window.location.hash;
    if (hash && $(`a[data-toggle="tab"][href="${hash}"]`).length) {
        $(`a[data-toggle="tab"][href="${hash}"]`).tab('show');
    }
});
$(function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>
@endpush
        {{-- Baris BARU untuk menyejajarkan kedua chart --}}
       <div class="row mt-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title font-weight-bold" 
                    style="color: #5C54AD;"
                    data-bs-toggle="tooltip"
                    title="Diagram menunjukkan perolehan data penjualan setiap kategori produk">
                    Data Penjualan
                </h3>
                <div style="height: 300px;">
                    <canvas id="penjualanChart"></canvas>
                </div>
            </div>
            <div class="card-footer">
                <div id="penjualanChartLegend" class="row">
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <p class="text-muted mb-2">
        > Diagram dibawah adalah perolehan data setiap produk yang telah teridentifikasi.
    </p>
        <div class="card">
            <div class="card-header bg-white border-0">
                <h3 class="card-title font-weight-bold">
                    Data per-Produk
                </h3>
            </div>
            <div class="card-body">
                <div style="height: 350px;">
                    <canvas id="produkChart"></canvas>
                </div>
            </div>
        </div>
    </div>
       </div>
    </div>
</div>
</div>
</div>
</div>
</div>
<section>

@push('css')
<style>
/* ====== General Typography ====== */
body {
    font-family: 'Inter', 'Segoe UI', sans-serif;
    font-size: 0.95rem;
    color: #333;
}

/* ====== Hero Banner ====== */
.hero-banner {
    background: linear-gradient(135deg, #6d4598, #a661c2, #c97aeb);
    border-radius: 20px;
    padding: 70px 20px;
    color: white;
    text-align: center;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.hero-banner h1 {
    font-size: 2.2rem;
    font-weight: 700;
    letter-spacing: 1px;
}

/* ====== Title Gradient ====== */
.wallpaper-text {
    background: linear-gradient(135deg, #ffffff, #f3d6ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 2px;
}

/* ====== Form & Button ====== */
.form-control {
    border-radius: 12px;
    transition: 0.3s ease;
}
.form-control:focus {
    box-shadow: 0 0 10px rgba(92, 84, 173, 0.3);
    border-color: #7d5fc2;
}

.btn-primary {
    background: linear-gradient(135deg, #6d4598, #a661c2);
    border: none;
    border-radius: 30px;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-primary:hover {
    background: linear-gradient(135deg, #5a367d, #8147be);
    transform: translateY(-2px);
}

/* ====== Small Box ====== */
.status-boxes {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 25px; /* jarak antar box */
    margin-bottom: 20px;
}
.small-box {
    position: relative;
    border-radius: 12px;
    padding: 20px;
    height: 140px; /* 🔹 lebih pendek, jadi persegi panjang */
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    transition: transform 0.2s ease-in-out;
}
.small-box .inner h3 {
    font-size: 30px;
    margin: 0;
    font-weight: bold;
}

.small-box .inner p {
    margin: 5px 0 0 0;
    font-size: 14px;
    font-weight: 600;
    text-align: center;
}
.small-box .icon {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 45px;
    opacity: 0.1;
}
.box-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.25);
}

/* ====== Card Charts ====== */
.card {
    border-radius: 18px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.08);
    border: none;
}
.card-title {
    font-size: 1rem;
    font-weight: 700;
    color: #5C54AD;
}
.tooltip .tooltip-arrow {
    display: none !important; /* sembunyikan panah */
}
.tooltip-inner {
    font-size: 11px;         /* kecilkan teks */
    border-radius: 4px;      /* sudut lebih rapat */
    padding: 3px 6px;        /* kotak lebih kecil */
    background-color: #333;  /* warna background */
    color: #fff;             /* warna teks */
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
}
.status-boxes {
    display: grid;
    grid-template-columns: repeat(5, 1fr); /* tetap 5 sejajar */
    gap: 15px; /* 🔹 perkecil jarak antar box */
    margin-bottom: 20px;
}
footer {
    text-align: left;
    padding: 10px 0;
    color: #555;
    font-size: 14px;
}

/* ====== Custom Colors ====== */
.bg-custom-ghost { background-color: #a1a6a7 !important; } /* biru langit */
.bg-custom-ask { background-color: #87b0ff !important; }
.bg-custom-follow-up { background-color: #A374FF !important; }
.bg-custom-hold { background-color: #5C54AD !important; }
.bg-custom-closing { background-color: #FF7373 !important; }

</style>
@endpush
@endsection
@push('js')
{{-- <script src="{{ asset('adminlte/plugins/chart.js/Chart.min.js') }}"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script>
    $(function () {
        // Daftarkan plugin secara global agar bisa dipakai semua chart
        Chart.register(ChartDataLabels);
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
                                color: '#fff',
                                font: { weight: 'bold' }
                            },
                            legend: { display: false },
                            title: {
                                display: true,
                                text: 'Data Customer',
                                font: { size: 20 },
                                align: 'start',
                                color: '#5C54AD'
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
                            barPercentage: 1
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
                            datalabels: {
                                display: false
                            },
                            legend: { 
                                display: false // Sembunyikan legenda
                            }
                        }
                    }
                });
            }
// --- Rate Customer Closing Chart ---
const rateClosingCanvas = document.getElementById('rateClosingLineChart');
if (rateClosingCanvas) {
    new Chart(rateClosingCanvas, {
    type: 'line',
    data: {
        labels: {!! json_encode($rateClosingLabels) !!}, // ['All','Produk','Survey','Pasang']
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
            tooltip: { enabled: true, mode: 'index', intersect: false },
            legend: { position: 'top', labels: { usePointStyle: true } }
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
                            plugins: {
                                datalabels: {
                                    display: false
                                },
                                legend: { position: 'top' } 
                            }
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
