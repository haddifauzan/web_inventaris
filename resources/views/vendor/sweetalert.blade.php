<script>
    @if (session('success'))
        const swalSuccess = Swal.fire({
            icon: 'success',
            title: 'Success',
            text: "{{ session('success') }}",
            showCloseButton: true,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            toast: true,
            position: 'center',
            width: '30%',
            position: 'top-end',
        });
    @elseif (session('error'))
        const swalError = Swal.fire({
            icon: 'error',
            title: 'Error',
            text: "{{ session('error') }}",
            showCloseButton: true,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            toast: true,
            position: 'center',
            width: '30%',
            position: 'top-end',
        });
    @elseif (session('throttle'))
        const swalThrottle = Swal.fire({
            icon: 'warning',
            title: 'Terlalu Banyak Percobaan',
            text: 'Coba lagi dalam {{ session('throttle') }} detik',
            timer: 3000,
            showCloseButton: true,
            showConfirmButton: false

        });

    @else
        @if ($errors->any())
            const swalError = Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ $errors->first() }}",
                showCloseButton: true,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                toast: true,
                position: 'center',
                width: '30%',
                position: 'top-end',
            });
        @endif
    @endif
</script>
