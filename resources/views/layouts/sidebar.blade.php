<!-- Tambah garis -->
<hr class="my-0 border-purple">
<div class="sidebar">
    <!-- Sidebar Menu -->
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-2 pb-2 mb-1 d-flex">
        <div class="image">
            <img src="{{ auth()->user()->image_url }}"alt="User Image" class="rounded-circle"
                style="width: 30px; height: 30px; object-fit: cover;">
        </div>
        <div class="info">
            <a href="{{ url('/profil') }}" class="d-block user-name">{{ Auth::user()->nama }}</a>
        </div>
    </div>
    <hr class="my-0 border-purple mx-n3">
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

            <!-- Dashboard -->
            <li class="nav-item">
                <a href="{{ url('/') }}" class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>

            <!-- Customer Section -->
            <li
                class="nav-item has-treeview {{ $activeMenu == 'customers' || $activeMenu == 'datainvoice' ? 'menu-open' : '' }}">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                        Customer
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview ml-2">
                    <li class="nav-item">
                        <a href="{{ url('/customers') }}"
                            class="nav-link {{ $activeMenu == 'customers' ? 'active' : '' }}">
                            <i class="fas fa-user-friends nav-icon"></i>
                            <p>Data Customer</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('datainvoice.index') }}"
                            class="nav-link {{ $activeMenu == 'datainvoice' ? 'active' : '' }}">
                            <i class="fas fa-file-invoice nav-icon"></i>
                            <p>Data Invoice</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Internal Section -->
            <li class="nav-item has-treeview {{ $activeMenu == 'produk' || $activeMenu == 'pic' ? 'menu-open' : '' }}">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-building"></i>
                    <p>
                        Internal
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview ml-2">
                    <li class="nav-item">
                        <a href="{{ url('/produk') }}" class="nav-link {{ $activeMenu == 'produk' ? 'active' : '' }}">
                            <i class="fas fa-box-open nav-icon"></i>
                            <p>Data Produk</p>
                        </a>
                    </li>
                    @if (Auth::user()->level->level_kode == 'ADM')
                        <li class="nav-item">
                            <a href="{{ url('/user') }}"
                                class="nav-link {{ $activeMenu == 'user' ? 'active' : '' }}">
                                <i class="fas fa-user-tie nav-icon"></i>
                                <p>Data User</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/keterangan-invoice') }}"
                                class="nav-link {{ $activeMenu == 'keterangan_invoice' ? 'active' : '' }}">
                                <i class="fas fa-file-invoice nav-icon"></i>
                                <p>Keterangan Invoice</p>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>

            <!-- Logout -->
            <li class="nav-item mt-3">
                <a href="{{ url('logout') }}" class="nav-link text-danger"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="nav-icon fas fa-sign-out-alt"></i>
                    <p>Logout</p>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>


        </ul>
    </nav>
</div>
<style>
    .user-name {
        color: white;
        text-decoration: none;
    }

    .user-name:hover {
        color: #4e479a;
        /* kuning (Bootstrap warning) */
    }

    .border-purple {
        border-color: #bf96d3 !important;
    }
</style>
