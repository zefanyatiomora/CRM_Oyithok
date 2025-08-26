<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'PWL Laravel Starter Code') }}</title>

   <meta name="csrf-token" content="{{ csrf_token() }}"> {{--Untuk mengirimkan token Laravel CSRF pada setiap  request ajax --}}


  <!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte\plugins\datatables-buttons\css//buttons.bootstrap4.min.css') }}">
<!-- SweetAlert -->
<link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
<!-- Theme style -->
<link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
<!-- Custom CSS -->
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">

@stack('css')
 
</head>
@push('css')
<style>
  .main-sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      overflow-y: auto;
  }

  .content-wrapper {
      margin-left: 250px; /* atau sesuai lebar sidebar */
      padding-top: 1rem;
  }

  body {
      overflow-x: hidden;
  }

  .sidebar {
      padding-bottom: 50px;
  }
</style>
@endpush

<body class="hold-transition sidebar-mini layout-fixed">
<!-- Site wrapper -->
<div class="wrapper">
  <!-- Navbar -->
  @include('layouts.header')
  <!-- /.navbar -->
  
  <!-- Main Sidebar Container -->
  {{-- <aside class="main-sidebar sidebar-light-success elevation-4"> --}}
    <aside class="main-sidebar custom-sidebar elevation-4">
      <!-- Brand Logo -->
      <a href="{{ url('/') }}" class="brand-link">
      <img src="{{ asset('adminlte/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">CRM</span>
    </a>
    
    <!-- Sidebar -->
    @include('layouts.sidebar')
    
    <!-- /.sidebar -->
  </aside>
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    @include('layouts.breadcrumb')
    
    <!-- Main content -->
    <section class="content">
      @yield('content')
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  @include('layouts.footer')
</div>
<!-- ./wrapper -->

<!-- Global Modal -->
<div class="modal fade" id="crudModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="modalContent">
            <!-- Konten akan dimuat via AJAX -->
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
{{-- Datatables & Plugins --}}
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

{{-- jquery validation --}}
<script src="{{ asset('adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/jquery-validation/additional-methods.min.js') }}"></script>
{{-- sweetalert2 --}}
<script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js')}}"></script>

<!-- AdminLTE App -->
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
<script>
  // untuk mengirimkan token Laravel CSRF pada setiap request ajax
  $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
  
  // === FUNGSI GLOBAL UNTUK MODAL CRUD ===
  window.openModal = function(url) {
    $('#modalContent').html(`
          <div class="text-center p-3">
            <div class="spinner-border text-primary" role="status">
                  <span class="sr-only">Loading...</span>
                  </div>
                  </div>
                  `);
                  $('#crudModal').modal('show');
                  $.ajax({
          url: url,
          type: 'GET',
          success: function(response) {
              $('#modalContent').html(response);
            },
          error: function() {
              $('#modalContent').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat data.</div>');
          }
      });
  }

</script>
@stack('js')
</body>
</html>
