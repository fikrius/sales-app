<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="/dashboard" class="brand-link">
    <span class="brand-text font-weight-light">SalesApp</span>
  </a>
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
        <li class="nav-item">
          <a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>
        
        @can('user-read')
        <li class="nav-header">ADMINISTRASI</li>
        <li class="nav-item">
          <a href="{{ route('users.index') }}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-users"></i>
            <p>Manajemen User</p>
          </a>
        </li>
        @endcan
        
        <li class="nav-header">TRANSAKSI</li>
        <li class="nav-item">
          <a href="{{ route('sales.index') }}" class="nav-link {{ request()->is('sales*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-shopping-cart"></i>
            <p>Penjualan</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('payments.index') }}" class="nav-link {{ request()->is('payments*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-money-bill-wave"></i>
            <p>Pembayaran</p>
          </a>
        </li>
        
        <li class="nav-header">MASTER DATA</li>
        <li class="nav-item">
          <a href="{{ route('items.index') }}" class="nav-link {{ request()->is('items*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-box"></i>
            <p>Data Item</p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
