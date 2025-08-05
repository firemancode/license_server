<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
      <img src="https://adminlte.io/themes/v3/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">License Server</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="{{ route('profile.edit') }}" class="d-block">{{ Auth::user()->name ?? 'Admin User' }}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          
          <!-- Dashboard -->
          <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <!-- Products -->
          <li class="nav-item {{ Route::is('admin.products.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ Route::is('admin.products.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-box"></i>
              <p>
                Produk
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('admin.products.index') }}" class="nav-link {{ Route::is('admin.products.index') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Daftar Produk</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.products.create') }}" class="nav-link {{ Route::is('admin.products.create') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tambah Produk</p>
                </a>
              </li>
            </ul>
          </li>

          <!-- Licenses -->
          <li class="nav-item {{ Route::is('admin.licenses.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ Route::is('admin.licenses.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-key"></i>
              <p>
                Lisensi
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('admin.licenses.index') }}" class="nav-link {{ Route::is('admin.licenses.index') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Daftar Lisensi</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.licenses.create') }}" class="nav-link {{ Route::is('admin.licenses.create') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tambah Lisensi</p>
                </a>
              </li>
            </ul>
          </li>

          <!-- Activations -->
          <li class="nav-item {{ Route::is('admin.activations.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ Route::is('admin.activations.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-globe"></i>
              <p>
                Aktivasi
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('admin.activations.index') }}" class="nav-link {{ Route::is('admin.activations.index') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Daftar Aktivasi</p>
                </a>
              </li>
            </ul>
          </li>

          <!-- API Documentation -->
          <li class="nav-header">API</li>
          <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}#api-docs" class="nav-link">
              <i class="nav-icon fas fa-book"></i>
              <p>Dokumentasi API</p>
            </a>
          </li>

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>