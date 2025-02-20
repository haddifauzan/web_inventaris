<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Login - RHGIS</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="{{ asset('images/logo.png') }}" rel="icon">
  <link href="{{ asset('images/logo.png') }}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.0/dist/sweetalert2.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.0/dist/sweetalert2.all.min.js"></script>

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
              <div class="d-flex align-items-center justify-content-between">
                <p class="d-flex align-items-center justify-content-center mx-auto" style="width: 80%">
                  <img src="images/logo.png" alt="" width="40" height="40">
                  <span class="d-none d-lg-block fw-bold ms-1 fs-2" style="color: #333;">RHGIS</span>
                  <span class="d-none d-lg-block ms-2" style="font-size: 14px; color: #333;">Rajawali Hiyoto Gagdet Inventory System</span>
                </p>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Login RGHIS</h5>
                    <p class="text-center small">Masukkan username dan password untuk login</p>
                  </div>

                  @if (session('throttle'))
                    <div class="alert alert-warning text-center" id="throttle-alert">
                      Terlalu banyak percobaan login. Silakan coba lagi dalam <span id="countdown">{{ session('throttle') }}</span> detik.
                    </div>
                    <script>
                      let seconds = {{ session('throttle') }};
                      const countdownElement = document.getElementById('countdown');

                      const countdown = setInterval(() => {
                        seconds--;
                        countdownElement.textContent = seconds;

                        if (seconds <= 0) {
                          clearInterval(countdown);
                          document.getElementById('throttle-alert').remove();
                        }
                      }, 1000);
                    </script>
                  @endif

                  <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('login.submit') }}">
                    @csrf

                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Username</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" class="form-control" id="yourUsername" value="{{ old('username') }}" required>
                        <div class="invalid-feedback">Please enter your username!</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" id="yourPassword" required>
                        <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword()" id="togglePassword"><i class="bi bi-eye" id="togglePasswordIcon"></i></span>
                        <div class="invalid-feedback">Please enter your password!</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Ingat saya</label>
                      </div>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Login</button>
                    </div>
                    <div class="col-12">
                        <p class="small mb-0">Lupa password? <a href="#" data-bs-toggle="modal" data-bs-target="#resetModal">Reset akun</a></p>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <!-- Modal Reset Akun -->
<div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="resetModalLabel">Reset Akun</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="resetForm" action="{{ route('resetCredential') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="kode_reset" class="form-label">Masukkan Kode Reset</label>
              <input type="text" class="form-control" id="kode_reset" name="kode_reset" required>
              <div class="invalid-feedback">Harap masukkan kode reset yang valid.</div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Reset Akun</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>


  <script>
    function togglePassword() {
      var x = document.getElementById("yourPassword");
      if (x.type === "password") {
        x.type = "text";
        document.getElementById("togglePasswordIcon").className = "bi bi-eye-slash";
      } else {
        x.type = "password";
        document.getElementById("togglePasswordIcon").className = "bi bi-eye";
      }
    }
  </script>


  @include('vendor.sweetalert')

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>