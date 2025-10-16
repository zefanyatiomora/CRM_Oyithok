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
        :root {
            --primary-color: #a66dd4;
            --primary-color-hover: #9559c3;
        }

        body.login-page {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f3e7ff;
            overflow: hidden;
            position: relative;
        }

        /* Wave background overlay */
        .wave-background {
            position: absolute;
            width: 100%;
            height: 100%;
            top:0;
            left:0;
            z-index:0;
            overflow:hidden;
        }

        .wave-background svg {
            position: absolute;
            bottom:0;
            width: 200%;
            height: 100%;
            animation: waveMove 10s linear infinite;
        }

        @keyframes waveMove {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* Login container */
        .login-container {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 550px;
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: fadeSlideUp 1s ease-out;
            z-index:1;
            position: relative;
        }

        .login-image-side {
            width: 50%;
            background: url('{{ asset('images/login.jpg') }}') center center/cover no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .login-form-side {
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            animation: fadeBounce 1.2s ease-out 0.3s forwards;
        }

        .login-box { width: 100%; box-shadow: none; margin: 0; }
        .login-box .card { border: none; box-shadow: none; }
        .login-logo a img { max-height: 80px; }

        /* Input & Button Styles */
        .form-control:focus { border-color: var(--primary-color); box-shadow: 0 0 12px rgba(166,109,212,0.4); transform: scale(1.02); }
        .input-group-text { transition: all 0.3s ease-in-out; }
        .input-group:focus-within .input-group-text { background: var(--primary-color); color:#fff; }
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); transition: all 0.3s ease; font-weight: bold; }
        .btn-primary:hover { transform: translateY(-3px) scale(1.03); box-shadow:0 10px 20px rgba(166,109,212,0.3); background-color: var(--primary-color-hover); border-color: var(--primary-color-hover); }

        /* Responsive */
        @media (max-width: 768px){
            .login-container { flex-direction: column; max-width: 400px; }
            .login-image-side { display: none; }
            .login-form-side { width: 100%; padding: 30px; }
        }

        /* Animations */
        @keyframes fadeSlideUp { 0% {opacity:0; transform:translateY(50px) scale(0.95);} 100% {opacity:1; transform:translateY(0) scale(1);} }
        @keyframes fadeBounce { 0% {opacity:0; transform:translateY(-20px);} 60% {opacity:1; transform:translateY(10px);} 100% {transform:translateY(0);} }
    </style>
</head>

<body class="hold-transition login-page">

    <!-- Wave Background -->
    <div class="wave-background">
        <svg viewBox="0 0 1440 320">
            <path fill="#a66dd4" fill-opacity="0.3" d="M0,160L48,176C96,192,192,224,288,213.3C384,203,480,149,576,149.3C672,149,768,203,864,224C960,245,1056,235,1152,208C1248,181,1344,139,1392,117.3L1440,96L1440,320L0,320Z"></path>
        </svg>
        <svg viewBox="0 0 1440 320">
            <path fill="#a66dd4" fill-opacity="0.5" d="M0,256L48,240C96,224,192,192,288,186.7C384,181,480,203,576,218.7C672,235,768,245,864,229.3C960,213,1056,171,1152,144C1248,117,1344,107,1392,101.3L1440,96L1440,320L0,320Z"></path>
        </svg>
    </div>

    <div class="login-container">
        <div class="login-image-side"></div>
        <div class="login-form-side">
            <div class="login-box">
                <div class="login-logo">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('images/Logo WPM.png') }}" alt="CRM Wallpaper ID">
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
                                    <div class="input-group-text"><span class="fas fa-user"></span></div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                                <div class="input-group-append">
                                    <div class="input-group-text"><span class="fas fa-lock"></span></div>
                                </div>
                            </div>
                            <small id="error-username" class="error-text text-danger d-block mb-2"></small>
                            <small id="error-password" class="error-text text-danger d-block mb-2"></small>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="show_password">
                                    <label class="form-check-label" for="show_password">Tampilkan password</label>
                                </div>
                                <div><button type="submit" class="btn btn-primary">Masuk</button></div>
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
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

$(document).ready(function(){
    $(".login-card-body").css("opacity",0).animate({opacity:1},800);

    $(".form-control").on("focus", function(){
        $(this).closest(".input-group").find(".input-group-text")
        .css("background","var(--primary-color)").css("color","#fff");
    }).on("blur", function(){
        $(this).closest(".input-group").find(".input-group-text")
        .css("background","#f3f0fa").css("color","#6c757d");
    });

    $("#form-login").validate({
        rules: { username:{required:true,minlength:4,maxlength:20}, password:{required:true,minlength:5,maxlength:20} },
        messages: { username:{required:"Username tidak boleh kosong", minlength:"Username minimal 4 karakter", maxlength:"Username maksimal 20 karakter"}, password:{required:"Password tidak boleh kosong", minlength:"Password minimal 5 karakter", maxlength:"Password maksimal 20 karakter"} },
        submitHandler: function(form){
            $.ajax({
                url: form.action, type: form.method, data: $(form).serialize(),
                success: function(response){
                    if(response.status){
                        Swal.fire({icon:'success',title:'Berhasil',text:response.message,showConfirmButton:true})
                        .then(()=>{window.location = response.redirect;});
                    } else {
                        $('.error-text').text('');
                        if(response.msgField){ $.each(response.msgField,function(prefix,val){ $('#error-'+prefix).text(val[0]); }); }
                        Swal.fire({icon:'error',title:'Gagal Login',text:response.message});
                    }
                },
                error: function(){
                    Swal.fire({icon:'error',title:'Terjadi Kesalahan',text:'Tidak dapat terhubung ke server.'});
                }
            });
            return false;
        },
        errorElement:'span',
        errorPlacement: function(error, element){ error.addClass('invalid-feedback'); element.closest('.input-group').append(error); },
        highlight:function(element){ $(element).addClass('is-invalid'); },
        unhighlight:function(element){ $(element).removeClass('is-invalid'); }
    });

    $('#show_password').on('change', function(){ $('#password').attr('type', this.checked?'text':'password'); });
});
</script>
</body>
</html>
