<div class="sidebar">

  <!-- Sidebar Menu -->
  <nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" 
        data-widget="treeview" role="menu" data-accordion="false">

      <!-- Dashboard -->
      <li class="nav-item">
        <a href="{{ url('/') }}" class="nav-link {{ ($activeMenu == 'dashboard') ? 'active' : '' }}">
          <i class="nav-icon fas fa-tachometer-alt"></i>
          <p>Dashboard</p>
        </a>
      </li>

      <!-- Customer Section -->
      <li class="nav-item has-treeview {{ ($activeMenu == 'customers' || $activeMenu == 'datainvoice') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-users"></i>
          <p>
            Customer
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview ml-2">
          <li class="nav-item">
            <a href="{{ url('/customers') }}" class="nav-link {{ ($activeMenu == 'customers') ? 'active' : '' }}">
              <i class="fas fa-user-friends nav-icon"></i>
              <p>Data Customer</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('datainvoice.index') }}" class="nav-link {{ ($activeMenu == 'datainvoice') ? 'active' : '' }}">
              <i class="fas fa-file-invoice nav-icon"></i>
              <p>Data Invoice</p>
            </a>
          </li>
        </ul>
      </li>

      <!-- Internal Section -->
      <li class="nav-item has-treeview {{ ($activeMenu == 'produk' || $activeMenu == 'pic') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-building"></i>
          <p>
            Internal
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview ml-2">
          <li class="nav-item">
            <a href="{{ url('/produk') }}" class="nav-link {{ ($activeMenu == 'produk') ? 'active' : '' }}">
              <i class="fas fa-box-open nav-icon"></i>
              <p>Data Produk</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ url('/pic') }}" class="nav-link {{ ($activeMenu == 'pic') ? 'active' : '' }}">
              <i class="fas fa-user-tie nav-icon"></i>
              <p>Data PIC</p>
            </a>
          </li>
        </ul>
      </li>

      <!-- Logout -->
      <li class="nav-item mt-3">
        <a href="{{ url('logout') }}" class="nav-link text-danger"
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

