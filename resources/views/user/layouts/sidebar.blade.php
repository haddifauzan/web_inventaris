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
        <a class="nav-link {{ request()->routeIs('komputer.index') || request()->routeIs('komputer.edit') || request()->routeIs('komputer.create') ? '' : 'collapsed' }}" href="{{ route('komputer.index') }}">
            <i class="bi bi-pc-display" style="font-size: 14px;"></i>
            <span class="ms-1">Komputer</span>
        </a>
      </li>
      
      <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('tablet.index') || request()->routeIs('tablet.edit') || request()->routeIs('tablet.create')? '' : 'collapsed' }}" href="{{ route('tablet.index') }}">
              <i class="bi bi-tablet" style="font-size: 14px;"></i>
              <span class="ms-1">Tablet</span>
          </a>
      </li>
      
      <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('switch.index') || request()->routeIs('switch.edit') || request()->routeIs('switch.create')? '' : 'collapsed' }}" href="{{ route('switch.index') }}">
              <i class="bi bi-router" style="font-size: 14px;"></i>
              <span class="ms-1">Switch</span>
          </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ (request()->routeIs('lokasi.index') || request()->routeIs('departemen.index') || request()->routeIs('ip-address.index') || request()->routeIs('ip-address.detail') || request()->routeIs('tipe-barang.index') ? '' : 'collapsed') }}" data-bs-target="#data-master-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-database"></i><span>Data Master</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="data-master-nav" class="nav-content collapse {{ (request()->routeIs('lokasi.index') || request()->routeIs('departemen.index') || request()->routeIs('ip-address.index') || request()->routeIs('ip-address.detail') || request()->routeIs('tipe-barang.index') ? 'show' : '') }}" data-bs-parent="#sidebar-nav">
          <li>
            <a href="{{route('lokasi.index')}}" class="{{ (request()->routeIs('lokasi.index') ? 'active' : '') }}">
              <i class="bi bi-geo-alt" style="font-size: 14px;"></i><span class="ms-1">Lokasi</span>
            </a>
          </li>
          <li>
            <a href="{{route('departemen.index')}}" class="{{ (request()->routeIs('departemen.index') ? 'active' : '') }}">
              <i class="bi bi-building" style="font-size: 14px;"></i><span class="ms-1">Departemen</span>
            </a>
          </li>
          <li>
            <a href="{{route('ip-address.index')}}" class="{{ (request()->routeIs('ip-address.index') || request()->routeIs('ip-address.detail') ? 'active' : '') }}">
              <i class="bi bi-geo" style="font-size: 14px;"></i><span class="ms-1">IP Address</span>
            </a>
          </li>
          <li>
            <a href="{{route('tipe-barang.index')}}" class="{{ (request()->routeIs('tipe-barang.index') ? 'active' : '') }}">
              <i class="bi bi-tag" style="font-size: 14px;"></i><span class="ms-1">Merk/Tipe Barang</span>
            </a>
          </li>
        </ul>
      </li>
    </ul>

  </aside><!-- End Sidebar-->