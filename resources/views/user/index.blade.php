@extends('layouts.template')

@section('content')
    <div class="card card-outline shadow-sm border-0">
      <div class="card-header bg-wallpaper-gradient position-relative" style="border-radius: 0.5rem 0.5rem 0 0; padding: 0.75rem 1.25rem;">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/user/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah User</button>
 
            </div>
        </div>
        <div class="card-body">
            @if (session('sucess'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group row">
                        <label class="col-1 control-labe; col-form-label">Filter:</label>
                        <div class="col-3">
                            <select class="form-control" id="level_id" name="level_id" required>
                                <option value="">- Semua -</option>
                                @foreach ($level as $item)
                                    <option value="{{ $item->level_id }}">{{ $item->level_nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Level Pengguna</small>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered table-striped table-hover table-sm" id="table-user">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Profil</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Level Pengguna</th>
                        <th>Aksi</th></tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div> 
@endsection

@push('css')
<style>
    .card-header.bg-gradient-primary {
        background: linear-gradient(135deg, #8147be, #c97aeb, #a661c2) !important;
        border-radius: 15px 15px 0 0;
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        color: #fff !important;
    }
    .bg-wallpaper-gradient {
        background: linear-gradient(135deg, #8147be, #c97aeb, #a661c2);
        border-radius: 15px 15px 0 0;
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        color: #fff;
    }
    #table-produk th {
        vertical-align: middle;
    }
</style>
@endpush

@push('js') l
  <script> 
  function modalAction(url = ''){ 
    $('#myModal').load(url,function(){ 
        $('#myModal').modal('show'); 
    }); 
  } 
    var dataUser;
    $(document).ready(function() { 
      dataUser= $('#table-user').DataTable({
          // serverSide: true, if using server-side processing 
          serverSide: true,      
          ajax: { 
              "url": "{{ url('user/list') }}", 
              "type": "POST", 
              "dataType": "json",
              "data": function (d) { // Send CSRF token with the request
                  d.level_id = $('#level_id').val();
              }
          }, 
          columns: [ 
            {
                // nomor urut from Laravel datatable addIndexColumn() 
              data: "DT_RowIndex",             
              className: "text-center", 
              orderable: false, 
              searchable: false     
            },
            {
                // Kolom baru untuk foto profil
                data: "image",
                className: "text-center",
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    // Jika ada gambar, tampilkan. Jika tidak, tampilkan placeholder.
                    var imageUrl = data ? '{{ asset("storage/") }}' + '/' + data : '{{ asset("adminlte/dist/img/default-avatar.png") }}';
                    return '<img src="' + imageUrl + '" alt="Foto Profil" class="img-circle img-size-32 mr-2" style="width: 32px; height: 32px; object-fit: cover;">';
                }
            },
            { 
              data: "username",                
              className: "", 
              orderable: true,     
              searchable: true     
            },{ 
              data: "nama",                
              className: "", 
              orderable: true,     
              searchable: true     
            },{ 
              // Fetching related data from 'level' relationship 
              data: "level.level_nama",                
              className: "", 
              orderable: false,     
              searchable: false     
            },{ 
              data: "aksi",                
              className: "", 
              orderable: false,     
              searchable: false     
            } 
          ] 
      }); 
      $('#level_id').on('change', function(){
        dataUser.ajax.reload();
      });
      
    }); 
  </script> 
@endpush
