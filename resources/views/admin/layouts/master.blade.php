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
  <link href="{{ asset('assets/css/style-ai.css') }}" rel="stylesheet">
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


  <!-- Tombol untuk membuka chatbot -->
  <div>
    <button class="btn btn-primary rounded-circle shadow-sm p-3 chat-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#chatOffcanvas" title="Chat dengan AI Assistant">
        <i class="bi bi-chat-dots fs-4"></i>
    </button>
  </div>

  <!-- Offcanvas Chatbot -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="chatOffcanvas" aria-labelledby="chatOffcanvasLabel" style="z-index: 99999;">
      <div class="offcanvas-header border-bottom">
          <h5 class="offcanvas-title" id="chatOffcanvasLabel">
              <i class="bi bi-robot me-2"></i> AI Assistant RHGIS
          </h5>
          <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body d-flex flex-column p-0">
          <div class="chat-messages flex-grow-1 p-3" id="chatContainer">
              <div class="chat-message bot-message animate__animated animate__fadeIn">
                  <div class="welcome-message">
                    <div class="mb-4">
                      <h5 class="mb-3">Halo! Saya adalah AI Assistant RHGIS 👋</h5>
                      <p class="mb-2">Saya dapat membantu Anda dengan informasi mengenai:</p>
                      
                      <div class="alert alert-info mb-3">
                        <strong><i class="bi bi-info-circle me-2"></i>Catatan:</strong>
                        Gunakan perintah <code>/cek</code> sebelum setiap pertanyaan
                      </div>

                      <div class="topics-list">
                        <h6 class="mb-2">Topik yang dapat ditanyakan:</h6>
                        <ul class="list-unstyled ms-3">
                          <li><i class="bi bi-check2-circle me-2"></i>Barang inventaris & pengelolaan barang</li>
                          <li><i class="bi bi-check2-circle me-2"></i>Komputer, tablet, dan switch</li>
                          <li><i class="bi bi-check2-circle me-2"></i>Status barang (baru/backup/aktif/pemusnahan)</li>
                          <li><i class="bi bi-check2-circle me-2"></i>Kelayakan barang</li>
                          <li><i class="bi bi-check2-circle me-2"></i>Lokasi barang</li>
                          <li><i class="bi bi-check2-circle me-2"></i>Informasi departemen</li>
                          <li><i class="bi bi-check2-circle me-2"></i>IP Address</li>
                          <li><i class="bi bi-check2-circle me-2"></i>Maintenance dan perawatan switch</li>
                          <li><i class="bi bi-check2-circle me-2"></i>Riwayat, OS, dan kepemilikan</li>
                          <li><i class="bi bi-check2-circle me-2"></i>Tahun perolehan</li>
                          <li><i class="bi bi-check2-circle me-2"></i>Total barang</li>
                        </ul>
                      </div>

                      <p class="mt-3 mb-0">
                        <i class="bi bi-chat-dots me-2"></i>Silakan ajukan pertanyaan Anda! Saya juga dapat membantu dengan pertanyaan di luar topik di atas.
                      </p>
                    </div>
                  </div>
              </div>
          </div>
          
          <div class="typing-indicator px-4 py-2" id="typingIndicator">
              <div class="typing-dots">
                  <span></span>
                  <span></span>
                  <span></span>
              </div>
              <span class="ms-2">AI RHGIS sedang mengetik</span>
          </div>
          
          <div class="chat-input-area px-3 pt-2 border-top">
            <div class="d-flex justify-content-between">
                <form id="chatForm" class="flex-grow-1">
                    <div class="input-group">
                        <textarea id="userMessage" class="form-control border-end-0" 
                          placeholder="Ketik pertanyaan Anda..." 
                          required 
                          autocomplete="off"
                          rows="1"
                          style="resize: none; min-height: 25px; max-height: 100px; overflow-y: auto;"
                          onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); this.form.dispatchEvent(new Event('submit')); }"
                        ></textarea>
                        <button class="btn btn-primary" type="submit">
                          <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Tombol Scroll -->
        <div class="d-flex justify-content-center my-2">
          <button id="scrollUpButton" class="btn btn-secondary mx-2" title="Scroll ke Atas">
              <i class="bi bi-arrow-up"></i>
          </button>
          <button id="refreshButton" class="btn btn-danger w-100" title="Refresh Percakapan">
            <i class="bi bi-arrow-clockwise"></i> Refresh Percakapan
          </button>
          <button id="scrollDownButton" class="btn btn-secondary mx-2" title="Scroll ke Bawah">
              <i class="bi bi-arrow-down"></i>
          </button>
      </div>
      </div>
  </div>

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
  <script src="{{ asset('assets/js/script-ai.js') }}"></script>
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
