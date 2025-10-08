<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | CRM Wallpaper Malang ID</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">

    <style>
        /* Custom Styles */
        :root {
            --primary-color: #a66dd4;
            --primary-color-hover: #9559c3;
            --background-gradient: linear-gradient(135deg, #f3e7ff, #e3d1f5);
        }

        .login-page {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f3e7ff;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3e%3cpath fill='%23a66dd4' fill-opacity='0.4' d='M0,160L48,176C96,192,192,224,288,213.3C384,203,480,149,576,149.3C672,149,768,203,864,224C960,245,1056,235,1152,208C1248,181,1344,139,1392,117.3L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3e%3c/path%3e%3cpath fill='%23a66dd4' fill-opacity='0.6' d='M0,256L48,240C96,224,192,192,288,186.7C384,181,480,203,576,218.7C672,235,768,245,864,229.3C960,213,1056,171,1152,144C1248,117,1344,107,1392,101.3L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3e%3c/path%3e%3c/svg%3e");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }
        
        .login-container {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 550px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-image-side {
            width: 50%;
            background: url('https://images.unsplash.com/photo-1556740738-b6a63e27c4df?q=80&w=1770&auto=format&fit=crop') center center/cover no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            color: white;
            padding: 20px;
            text-align: center;
        }
        /* Overlay to make text readable */
        /* .login-image-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            background: rgba(103, 58, 183, 0.4);
            border-radius: 15px 0 0 15px;
        } */
        /* Tambahkan di style */
/* Animasi Fade & Slide untuk container */
@keyframes fadeSlide {
    0% {
        opacity: 0;
        transform: translateY(40px) scale(0.98);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Terapkan animasi ke login container */
.login-container {
    animation: fadeSlide 1s ease-out;
}

/* Efek input glow */
.form-control {
    transition: all 0.3s ease-in-out;
}
.form-control:focus {
    transform: scale(1.02);
    box-shadow: 0 0 12px rgba(166, 109, 212, 0.4);
}

/* Input icon ikut glow */
.input-group-text {
    transition: all 0.3s ease-in-out;
}
.input-group:focus-within .input-group-text {
    background: var(--primary-color);
    color: #fff;
    box-shadow: 0 0 8px rgba(166, 109, 212, 0.6);
}

/* Tombol lebih hidup */
.btn-primary {
    transition: all 0.3s ease;
}
.btn-primary:hover {
    transform: translateY(-3px) scale(1.03);
    box-shadow: 0 10px 20px rgba(166, 109, 212, 0.3);
}
.btn-primary:active {
    transform: scale(0.98);
}

        .login-form-side {
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }
        
        .login-box {
            width: 100%;
            box-shadow: none;
            margin: 0;
        }

        .login-box .card {
            border: none;
            box-shadow: none;
        }

        .login-logo a {
            color: #333;
            font-weight: 700;
        }
        .login-logo b {
            color: var(--primary-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: bold;
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-color-hover);
            border-color: var(--primary-color-hover);
        }

        .icheck-primary>input:first-child:checked+label::before {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(166, 109, 212, 0.25);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                min-height: auto;
                max-width: 400px; /* Lebar maksimum di mobile */
            }

            .login-image-side {
                display: none; /* Sembunyikan gambar di layar kecil */
            }

            .login-form-side {
                width: 100%;
                padding: 30px;
            }
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="login-container">
        <div class="login-image-side">
            </div>

        <div class="login-form-side">
            <div class="login-box">
                <div class="login-logo">
                    {{-- <a href="{{ url('/') }}"><b>CRM</b>WALLPAPER ID</a> --}}
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('images/Logo WPM.png') }}" 
                            alt="CRM Wallpaper ID" 
                            style="max-height: 80px;">
                    </a>
                </div>
                <div class="card">
                    <div class="card-body login-card-body">
                        <p class="login-box-msg">Masukkan Username dan Password.</p>

                        <form action="{{ url('login') }}" method="POST" id="form-login">
                            @csrf
                            <div class="input-group mb-3">
                                <input type="text" id="username" name="username" class="form-control" placeholder="Username">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-user"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                            <small id="error-username" class="error-text text-danger d-block mb-2"></small>
                            <small id="error-password" class="error-text text-danger d-block mb-2"></small>

                            <div class="row">
                                <div class="col-8">
                                    <div class="icheck-primary">
                                        <input type="checkbox" id="remember">
                                        <label for="remember">Remember Me</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
    
    <script>
    // AJAX Setup untuk CSRF Token (tidak perlu meta tag di head)
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
    $(document).ready(function() {
    // Animasi fade untuk card form
    $(".login-card-body").css("opacity", "0").animate({opacity: 1}, 800);

  $(".form-control").on("focus", function() {
        $(this).closest(".input-group").find(".input-group-text")
            .css("background", "var(--primary-color)")
            .css("color", "#fff");
    }).on("blur", function() {
        $(this).closest(".input-group").find(".input-group-text")
            .css("background", "#f3f0fa")
            .css("color", "#6c757d");
    });
});

    $(document).ready(function() {
        $("#form-login").validate({
            rules: {
                username: { required: true, minlength: 4, maxlength: 20 },
                password: { required: true, minlength: 5, maxlength: 20 }
            },
            messages: { // Pesan error custom
                username: {
                    required: "Username tidak boleh kosong",
                    minlength: "Username minimal 4 karakter",
                    maxlength: "Username maksimal 20 karakter"
                },
                password: {
                    required: "Password tidak boleh kosong",
                    minlength: "Password minimal 5 karakter",
                    maxlength: "Password maksimal 20 karakter"
                }
            },
            submitHandler: function(form) { // Fungsi ini berjalan saat form valid
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) { // Jika sukses
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: true
                            }).then(function() {
                                window.location = response.redirect;
                            });
                        } else { // Jika gagal
                            $('.error-text').text('');
                            // Menampilkan error validasi dari server
                            if (response.msgField) {
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Login',
                                text: response.message
                            });
                        }
                    },
                    error: function() { // Jika terjadi error AJAX
                         Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Tidak dapat terhubung ke server.'
                        });
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.input-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });
    </script>
</body>
</html>