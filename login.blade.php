<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from /demo/html/template/login by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 08 Aug 2025 05:20:44 GMT -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="HRXpert - Smart HR Management System for employee management, payroll, attendance, and performance tracking.">
    <meta name="keywords"
        content="HRXpert, HR management, employee management, payroll, attendance, leave tracking, performance evaluation, HR software">
    <meta name="author" content="Mahendra Sahu">
    <meta name="robots" content="index, follow">

    <!-- Title -->
    <title>Neural Info Solution
    </title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/img/favicon.png">


    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('frontent/assets/img/favicon.png') }}">

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('frontent/assets/img/apple-touch-icon.png') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('frontent/assets/css/bootstrap.min.css') }}">

    <!-- Feather CSS -->
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/icons/feather/feather.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/fontawesome/css/all.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('frontent/assets/css/style.css') }}">
</head>

<body class="bg-white">

    <div id="global-loader" style="display: none;">
        <div class="page-loader"></div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <div class="container-fuild">
            <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
                <div class="row">
                    <div class="col-lg-5">
                        <div
                            class="login-background position-relative d-lg-flex align-items-center justify-content-center d-none flex-wrap vh-100">
                            <div class="bg-overlay-img">
                                <img src="{{ asset('frontent/assets/img/bg/bg-01.png') }}" class="bg-1"
                                    alt="Img">
                                <img src="{{ asset('frontent/assets/img/bg/bg-02.png') }}" class="bg-2"
                                    alt="Img">
                                <img src="{{ asset('frontent/assets/img/bg/bg-03.png') }}" class="bg-3"
                                    alt="Img">
                            </div>
                            <div class="authentication-card w-100">
                                <div class="authen-overlay-item border w-100">
                                    <h1 class="text-white display-1">Empowering people <br> through seamless HR <br>
                                        management.</h1>
                                    <div class="my-4 mx-auto authen-overlay-img">
                                        <img src="{{ asset('frontent/assets/img/bg/authentication-bg-01.png') }}"
                                            alt="Img">
                                    </div>
                                    <div>
                                        <p class="text-white fs-20 fw-semibold text-center">Efficiently manage your
                                            Employee, streamline <br> operations effortlessly.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <style>
                        .logo-text {
                            font-family: 'Inter', sans-serif;
                            /* Modern font */
                            font-size: 2.5rem;
                            /* Large heading */
                            font-weight: 700;
                            /* Bold */
                            color: #00B4EE;
                            /* Paytm blue */
                            letter-spacing: 2px;
                            /* Professional spacing */
                            /* text-transform: uppercase; */
                            /* Uppercase letters */
                            transition: color 0.3s ease;
                        }
                    </style>
                    <div class="col-lg-7 col-md-12 col-sm-12">
                        <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap">
                            <div class="col-md-7 mx-auto vh-100">
                                <div class="position-fixed top-0 end-0 p-3" style="z-index: 1100;">
                                    @if (session('success'))
                                        <div class="toast align-items-center text-white bg-success border-0 show"
                                            role="alert">
                                            <div class="d-flex">
                                                <div class="toast-body">
                                                    {{ session('success') }}
                                                </div>
                                                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                                    data-bs-dismiss="toast"></button>
                                            </div>
                                        </div>
                                    @endif

                                    @if (session('error'))
                                        <div class="toast align-items-center text-white bg-danger border-0 show"
                                            role="alert">
                                            <div class="d-flex">
                                                <div class="toast-body">
                                                    {{ session('error') }}
                                                </div>
                                                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                                    data-bs-dismiss="toast"></button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <form action="{{ route('login') }}" class="vh-100">
                                    @csrf
                                    <div class="vh-100 d-flex flex-column justify-content-between p-4 pb-0">
                                        <div class="logo-text mx-auto mb-5 text-center">
                                            <img src="{{ asset('frontent/assets/img/icons/logo_1.png') }}"
                                                alt="Img">
                                        </div>


                                        <div class="">
                                            <div class="text-center mb-3">
                                                <h2 class="mb-2">Sign In</h2>
                                                <p class="mb-0">Please enter your details to sign in</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email Address</label>
                                                <div class="input-group">
                                                    <input type="text" value="{{ old('email') }}" name="email"
                                                        class="form-control border-end-0">
                                                    <span class="input-group-text border-start-0">
                                                        <i class="ti ti-mail"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Password</label>
                                                <div class="pass-group">
                                                    <input type="password" value="{{ old('password') }}"
                                                        class="pass-input form-control" name="password">
                                                    <span class="ti toggle-password ti-eye-off"></span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <div class="d-flex align-items-center">
                                                    {{-- <div class="form-check form-check-md mb-0">
                                                        <input class="form-check-input" id="remember_me"
                                                            name="remember_me" type="checkbox">
                                                        <label for="remember_me"
                                                            class="form-check-label mt-0">Remember
                                                            Me</label>
                                                    </div> --}}
                                                </div>
                                                {{-- <div class="text-end">
                                                    <a href="#" class="link-danger">Forgot Password?</a>
                                                </div> --}}
                                            </div>
                                            <div class="mb-3">
                                                <button type="submit" class="btn btn-primary w-100">Sign In</button>
                                            </div>
                                            {{-- <div class="text-center">
                                                <h6 class="fw-normal text-dark mb-0">Don’t have an account?
                                                    <a href="#" class="hover-a"> Create Account</a>
                                                </h6>
                                            </div> --}}


                                        </div>
                                        <div class="mt-5 pb-4 text-center">
                                            <p class="mb-0 text-gray-9">Copyright &copy; 2026 - Neural Info Solutionv
                                                Pvt. Ltd
                                            </p>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Main Wrapper -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            var toastList = toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl, {
                    delay: 30000
                });
            });
            toastList.forEach(toast => toast.show());
        });
    </script>

    <!-- jQuery -->
    <script src="{{ asset('frontent/assets/js/jquery-3.7.1.min.js') }}"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('frontent/assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Feather Icon JS -->
    <script src="{{ asset('frontent/assets/js/feather.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('frontent/assets/js/script.js') }}"></script>

</body>


<!-- Mirrored from /demo/html/template/login by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 08 Aug 2025 05:20:44 GMT -->

</html>
