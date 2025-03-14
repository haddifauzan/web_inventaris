<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>RHGIS | @yield('title')</title>

  <meta content="" name="description">
  <meta content="" name="keywords">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Favicons -->
  <link href="{{ asset('images/logo.png') }}" rel="icon" type="image/png">
  <link href="{{ asset('images/logo.png') }}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.0/dist/sweetalert2.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.0/dist/sweetalert2.all.min.js"></script>

  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/datatable/datatables.min.css') }}" rel="stylesheet">

  <style>
    /* Overlay Loading Screen */
    #loading-screen {
      position: fixed;
      width: 100%;
      height: 100%;
      background: rgba(75, 75, 75, 0.318);
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .spinner {
      --d: 24.6px;
      width: 4.5px;
      height: 4.5px;
      border-radius: 50%;
      color: #00a2ff;
      box-shadow: calc(1*var(--d))      calc(0*var(--d))     0 0,
              calc(0.707*var(--d))  calc(0.707*var(--d)) 0 1.1px,
              calc(0*var(--d))      calc(1*var(--d))     0 2.2px,
              calc(-0.707*var(--d)) calc(0.707*var(--d)) 0 3.4px,
              calc(-1*var(--d))     calc(0*var(--d))     0 4.5px,
              calc(-0.707*var(--d)) calc(-0.707*var(--d))0 5.6px,
              calc(0*var(--d))      calc(-1*var(--d))    0 6.7px;
      animation: spinner-a90wxe 1s infinite steps(8);
    }

    @keyframes spinner-a90wxe {
      100% {
        transform: rotate(1turn);
      }
    }
  </style>

  <!-- Template Main CSS File -->
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

</head>

<body>

  <!-- Loading Screen -->
  <div id="loading-screen">
    <div class="spinner"></div>
  </div>

  @include('admin.layouts.navbar')
  @include('admin.layouts.sidebar')
  @include('admin.layouts.content')
  @include('admin.layouts.footer')

  @include('vendor.sweetalert')

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
  <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/quill/quill.js') }}"></script>
  <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
  <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('assets/vendor/datatable/datatables.min.js') }}"></script>

  <!-- Template Main JS File -->
  <script src="{{ asset('assets/js/main.js') }}"></script>

  <script>
    $(document).ready(function () {
      // Hilangkan loading saat halaman selesai dimuat
      $("#loading-screen").fadeOut();
  
      // Deteksi ketika pengguna menekan tombol "Back" atau "Forward"
      $(window).on("pageshow", function (event) {
        if (event.originalEvent && event.originalEvent.persisted) {
          $("#loading-screen").fadeOut();
        }
      });
    });
  
    $(document).on("click", "a", function (event) {
      let isOffcanvas = $(this).attr("data-bs-toggle") === "offcanvas";
      let hasHref = $(this).attr("href") && $(this).attr("href") !== "#" && !$(this).attr("href").startsWith("javascript:void(0)");
  
      if (!isOffcanvas && hasHref) {
        $("#loading-screen").fadeIn();
      }
    });
  </script>  

</body>

</html>
