<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>BMS</title>
    {{-- <link rel="icon" href="{{ asset('images/icon.png') }}" type="image/png"> --}}

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('templates/dist/css/adminlte.min.css') }}">

    <style>
    .form-control-icon {
            position: relative;
        }

        .form-control-icon i {
            position: absolute;
            top: 50%;
            left: 20px;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 13px;
            pointer-events: none;
        }

        .form-control-icon input.form-control {
            padding-left: 2.5rem;
        }

        .form-control,
        .btn {
            border-radius: 50rem !important;
        }
        .input-filled {
            font-size: 1rem;
        font-weight: 600;
        color: #242424;
    }

    .login-box {
    width: 450px;
    max-width: 90%;
    font-size: 1rem;

}
::placeholder {
    font-size: 1rem;
    font-weight: 600;
    color: #999999;
    opacity: 1;
}

.btn-login {
    background-color: #333333; /* Gradasi biru awal */
    color: white;
    font-weight: bold;
    border-radius: 50rem;
    transition: background 0.3s ease;
    border: none;
}

.btn-login:hover {
    background: linear-gradient(to right, #1e71ff, #1a6af3); /* Gradasi saat hover */
    color: white;
}


    </style>

</head>

<body class="hold-transition overlay login-page">
    <div class="login-box p-5" style="background-color: #fff; border-radius: 20px;">
            {{-- <div class=" text-center d-col items-center justify-content-center">
                <img class="mb-2" src="{{ asset('images/logo.png') }}" alt="logo" width="120px" height="auto">
                <h2 style="font-size: 25px; color: #242424;"><b>Login</b></h2>
            </div> --}}
            <div class="card-body">
                <form method="POST" action="{{ route('admin.loginPost') }}">
                    @csrf

                    <div class="form-group mb-2 form-control-icon">
                        <i class="fas fa-user fa-sm"></i>
                        <input type="text"
                               class="form-control form-control-lg w-100 @error('username') is-invalid @enderror"
                               name="username" id="username"
                               placeholder="Username" value="{{ old('username') }}" required>
                        @error('username')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>


                    <div class="form-group mb-2 form-control-icon">
                        <i class="fas fa-lock fa-sm"></i>
                        <input type="password" class="form-control form-control-lg w-100 @error('password') is-invalid @enderror"
                               name="password" id="password"
                               placeholder="Password" required>
                        @error('password')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>


                    {{-- Button --}}
                    <div class="form-group">
                        <button type="submit" class="btn btn-lg w-100 btn-login" >
                            <span class="font-weight-bold">Login</span>
                        </button>
                    </div>
                </form>
            </div>
            <!-- /.card-body -->
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="{{ asset('templates/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('templates/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('templates/dist/js/adminlte.min.js') }}"></script>
    <script>
        const input = document.getElementById('username');

        input.addEventListener('input', function () {
            if (this.value.trim() !== '') {
                this.classList.add('input-filled');
            } else {
                this.classList.remove('input-filled');
            }
        });

        // Jalankan saat halaman dimuat (untuk old value)
        window.addEventListener('DOMContentLoaded', () => {
            input.dispatchEvent(new Event('input'));
        });
    </script>





</body>

</html>
