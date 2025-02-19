<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('user.dashboard') ? '' : 'collapsed' }}" href="{{ route('user.dashboard') }}">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
          </a>
      </li>
      
      <li class="nav-heading">Inventaris Komputer</li>
      
      <li class="nav-item">
          <a class="nav-link collapsed" href="javascript:void(0)">
              <i class="bi bi-list-ul"></i>
              <span>Semua Barang</span>
          </a>
      </li>
      
      <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('komputer.index') || request()->routeIs('komputer.edit') || request()->routeIs('komputer.create') ? '' : 'collapsed' }}" href="{{ route('komputer.index') }}">
              <i class="bi bi-pc-display" style="font-size: 14px;"></i>
              <span class="ms-1">Komputer</span>
          </a>
      </li>
      
      <li class="nav-item">
          <a class="nav-link collapsed" href="#">
              <i class="bi bi-tablet" style="font-size: 14px;"></i>
              <span class="ms-1">Tablet</span>
          </a>
      </li>
      
      <li class="nav-item">
          <a class="nav-link collapsed" href="#">
              <i class="bi bi-router" style="font-size: 14px;"></i>
              <span class="ms-1">Switch</span>
          </a>
      </li>
    </ul>

  </aside><!-- End Sidebar-->