<div class="modal-dialog modal-lg">
  <div class="modal-content border-0 shadow-lg rounded-4">
    
    <!-- HEADER -->
    <div class="modal-header bg-wallpaper-gradient text-white rounded-top-4">
      <h5 class="modal-title fw-semibold">
        <i class="fas fa-user-circle me-2"></i> Detail User
      </h5>
    </div>

    <!-- BODY -->
    <div class="modal-body px-4 py-4 bg-light">
      <div class="row g-4">
        
        <!-- PROFIL -->
        <div class="col-md-4 text-center">
          <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-wallpaper-gradient text-white fw-semibold rounded-top-4">
              <i class="fas fa-user me-2"></i> Profil
            </div>
            <div class="card-body bg-white rounded-bottom-4">
              <img src="{{ $user->image ? asset('storage/' . $user->image) : asset('adminlte/dist/img/default-avatar.png') }}" 
                   class="rounded-circle border border-3 border-info shadow-sm mb-3"
                   style="width:130px; height:130px; object-fit:cover;">
              <h5 class="fw-bold mb-1 text-dark">{{ $user->nama }}</h5>
              <p class="text-muted mb-2">{{ '@' . $user->username }}</p>
              <span class="badge bg-info text-white px-3 py-2 rounded-pill">
                {{ $user->level->level_nama ?? 'Tidak Diketahui' }}
              </span>
            </div>
          </div>
        </div>

        <!-- INFORMASI AKUN -->
        <div class="col-md-8">
          <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-wallpaper-gradient text-white fw-semibold rounded-top-4">
              <i class="fas fa-id-card me-2"></i> Informasi Akun
            </div>
            <div class="card-body bg-white rounded-bottom-4">
              <table class="table table-borderless mb-0">
                <tr>
                  <th class="text-muted" width="35%">Username</th>
                  <td>{{ $user->username }}</td>
                </tr>
                <tr>
                  <th class="text-muted">Password</th>
                  <td><code>{{ substr($user->password, 0, 10) . '...' }}</code></td>
                </tr>
                <tr>
                  <th class="text-muted">Level Pengguna</th>
                  <td>{{ $user->level->level_nama ?? '-' }}</td>
                </tr>
                <tr>
                  <th class="text-muted">No. HP</th>
                  <td>{{ $user->nohp ?? '-' }}</td>
                </tr>
                <tr>
                  <th class="text-muted">Alamat</th>
                  <td>{{ $user->alamat ?? '-' }}</td>
                </tr>
              </table>
            </div>
          </div>
        </div>

      </div>

      <!-- TANDA TANGAN -->
      <div class="mt-4">
        <div class="card border-0 shadow-sm rounded-4">
          <div class="card-header bg-wallpaper-gradient text-white fw-semibold rounded-top-4">
            <i class="fas fa-pen-fancy me-2"></i> Tanda Tangan
          </div>
          <div class="card-body bg-white text-center rounded-bottom-4">
            @if($user->ttd)
              <a href="{{ asset('storage/' . $user->ttd) }}" target="_blank">
                <img src="{{ asset('storage/' . $user->ttd) }}" 
                     alt="Tanda Tangan" 
                     class="img-fluid shadow-sm rounded"
                     style="max-height: 120px; object-fit: contain;">
              </a>
            @else
              <span class="text-muted fst-italic">Belum ada file tanda tangan</span>
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- FOOTER -->
    <div class="modal-footer border-0 bg-white rounded-bottom-4">
      <a href="{{ route('user.index') }}" class="btn btn-secondary px-4 rounded-pill">
        <i class="fas fa-times me-1"></i> Tutup
      </a>
    </div>
  </div>
</div>

@push('css')
<style>
  .bg-wallpaper-gradient {
    background: linear-gradient(135deg, #007bff, #6f42c1);
  }
</style>
@endpush
