<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">

  <div class="d-flex align-items-center justify-content-between">
    <a href="{{ route('user.dashboard') }}" class="logo d-flex align-items-center">
      <img src="{{ asset('images/logo.png') }}" alt="">
      <span class="d-none d-lg-block fw-bold" style="color: #333;">RHGIS</span>
      <span class="d-none d-lg-block ms-2 m-0 p-0" style="font-size: 12px; color: #333;">Rajawali Hiyoto Gadget Inventory System</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div><!-- End Logo -->

  @include('vendor.search-bar')
  <!-- End Search Bar -->

  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">

      <li class="nav-item d-block d-lg-none">
        <a class="nav-link nav-icon search-bar-toggle " href="#">
          <i class="bi bi-search"></i>
        </a>
      </li><!-- End Search Icon-->

      <li class="nav-item dropdown pe-3">

        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <img src="{{ asset('images/user.png') }}" alt="Profile" class="rounded-circle">   
          <span class="d-none d-md-block dropdown-toggle ps-2 mx-2">{{ strtoupper(Auth::user()->username) }}</span>
        </a><!-- End Profile Iamge Icon -->

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li class="dropdown-header">
            <h6>{{ strtoupper(Auth::user()->username) }}</h6>
            <span>Pengelola Aplikasi</span>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li> 

          <li>
            <button class="dropdown-item d-flex align-items-center" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
              <i class="bi bi-gear"></i>
              <span>Pengaturan Akun</span>
            </button>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li>
            <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
              <i class="bi bi-question-circle"></i> 
              <span>Tentang Aplikasi</span>
            </a>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>

          <li>
            <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
              <i class="bi bi-box-arrow-right"></i>
              <span>Log Out</span>
            </a>
          </li>
        </ul><!-- End Profile Dropdown Items -->
      </li><!-- End Profile Nav -->

    </ul>
  </nav><!-- End Icons Navigation -->

</header><!-- End Header -->

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah anda yakin untuk logout?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-danger">Ya, Keluar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
  <div class="offcanvas-header bg-light">
      <h5 id="offcanvasRightLabel" class="offcanvas-title">Pengaturan Akun</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
      <div class="mb-3">
          <label for="usernameLama" class="form-label">Username Saat Ini</label>
          <input type="text" class="form-control" id="usernameLama" value="{{ Auth::user()->username }}" readonly disabled>
      </div>
      <hr>
      <form action="{{ route('updateCredential', Auth::user()->id_user) }}" method="POST" id="updateCredentialForm">
          @csrf
          @method('PUT')
          <div class="mb-3 position-relative">
              <label for="usernameBaru" class="form-label">Username Baru</label>
              <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person"></i></span>
                  <input type="text" class="form-control @error('username') is-invalid @enderror" id="usernameBaru" name="username" value="{{ old('username') }}" required>
                  @error('username')
                      <div class="invalid-feedback">
                          {{ $message }}
                      </div>
                  @enderror
              </div>
          </div>
          <div class="mb-3 position-relative">
              <label for="passwordBaru" class="form-label">Password Baru</label>
              <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-lock"></i></span>
                  <input type="password" class="form-control @error('password') is-invalid @enderror" id="passwordBaru" name="password" value="{{ old('password') }}" required>
                  <button class="btn btn-secondary" type="button" id="togglePassword">
                      <i class="bi bi-eye-slash"></i>
                  </button>
                  @error('password')
                      <div class="invalid-feedback">
                          {{ $message }}
                      </div>
                  @enderror
              </div>
          </div>
          <div class="mb-3 position-relative">
              <label for="konfirmasiPassword" class="form-label">Konfirmasi Password</label>
              <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-lock"></i></span>
                  <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="konfirmasiPassword" name="password_confirmation" value="{{ old('password_confirmation') }}" required>
                  <button class="btn btn-secondary" type="button" id="toggleKonfirmasiPassword">
                      <i class="bi bi-eye-slash"></i>
                  </button>
                  @error('password_confirmation')
                      <div class="invalid-feedback">
                          {{ $message }}
                      </div>
                  @enderror
              </div>
          </div>
          <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
      </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('updateCredentialForm');
    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);

    const togglePasswordVisibility = (toggleButton, passwordField) => {
        toggleButton.addEventListener('click', function () {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });
    };

    const togglePassword = document.querySelector('#togglePassword');
    const passwordBaru = document.querySelector('#passwordBaru');
    togglePasswordVisibility(togglePassword, passwordBaru);

    const toggleKonfirmasiPassword = document.querySelector('#toggleKonfirmasiPassword');
    const konfirmasiPassword = document.querySelector('#konfirmasiPassword');
    togglePasswordVisibility(toggleKonfirmasiPassword, konfirmasiPassword);
});
</script>