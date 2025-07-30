<div class="sidebar">
      {{-- <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../../dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Alexander Pierce</a>
        </div>
      </div> --}}

      <!-- Sidebar Menu -->
      <nav class="mt-2"> 
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" 
    role="menu" data-accordion="false"> 
          <li class="nav-item"> 
            <a href="{{ url('/') }}" class="nav-link  {{ ($activeMenu == 'dashboard')? 
    'active' : '' }} "> 
              <i class="nav-icon fas fa-tachometer-alt"></i> 
              <p>Dashboard</p> 
            </a> 
          </li> 
              <li class="nav-item"> 
                <a href="{{ url('/customers') }}" class="nav-link {{ ($activeMenu == 'customers')? 
        'active' : '' }} "> 
                  <i class="nav-icon fas fa-layer-group"></i> 
                  <p>Data Customer</p> 
                </a> 
              </li> 

              <li class="nav-header">Data Produk</li> 
              <li class="nav-item"> 
                <a href="{{ url('/kategori') }}" class="nav-link {{ ($activeMenu == 
        'kategori')? 'active' : '' }} "> 
                  <i class="nav-icon far fa-bookmark"></i> 
                  <p>List Produk</p> 
                </a> 
              </li> 
        
              <li class="nav-item"> 
                <a href="{{ url('/form-kebutuhan') }}" class="nav-link {{ ($activeMenu == 'formkebutuhan')? 'active' : '' }}">
                  <i class="nav-icon fas fa-cash-register"></i>
                    <p>Form Kebutuhan</p>
                  </a>
              </li> 
              <li class="nav-item">
                <a href="{{ url('logout') }}" class="nav-link"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                  <i class="nav-icon fas fa-sign-out-alt"></i>
                  <p>Logout</p>
                </a>
                <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                  @csrf
                </form>
              </li>
            </ul> 
          </nav> 
        </div> 