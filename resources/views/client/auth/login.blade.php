<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>{{ $title ?? 'Client Login' }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('frontent/assets/img/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('frontent/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontent/assets/css/style.css') }}">
</head>

<body class="bg-white">
    <div class="main-wrapper">
        <div class="container-fuild">
            <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="login-background position-relative d-lg-flex align-items-center justify-content-center d-none flex-wrap vh-100">
                            <div class="bg-overlay-img">
                                <img src="{{ asset('frontent/assets/img/bg/bg-01.png') }}" class="bg-1" alt="Img">
                                <img src="{{ asset('frontent/assets/img/bg/bg-02.png') }}" class="bg-2" alt="Img">
                                <img src="{{ asset('frontent/assets/img/bg/bg-03.png') }}" class="bg-3" alt="Img">
                            </div>
                            <div class="authentication-card w-100">
                                <div class="authen-overlay-item border w-100">
                                    <h1 class="text-white display-1">Client support<br>for project issues.</h1>
                                    <div class="my-4 mx-auto authen-overlay-img">
                                        <img src="{{ asset('frontent/assets/img/bg/authentication-bg-01.png') }}" alt="Img">
                                    </div>
                                    <p class="text-white fs-20 fw-semibold text-center">Raise, track, and resolve project tickets in one secure portal.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7 col-md-12 col-sm-12">
                        <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap">
                            <div class="col-md-7 mx-auto vh-100">
                                <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

                                @if ($errors->any())
                                    <div class="alert alert-danger mt-4">
                                        {{ $errors->first() }}
                                    </div>
                                @endif

                                <form action="{{ route('client.login.submit') }}" method="POST" class="vh-100">
                                    @csrf
                                    <div class="vh-100 d-flex flex-column justify-content-between p-4 pb-0">
                                        <div class="mx-auto mb-5 text-center">
                                            <img src="{{ asset('frontent/assets/img/icons/logo_1.png') }}" alt="Logo">
                                        </div>

                                        <div>
                                            <div class="text-center mb-3">
                                                <h2 class="mb-2">Client Sign In</h2>
                                                <p class="mb-0">Access your projects and support tickets</p>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Email Address</label>
                                                <div class="input-group">
                                                    <input type="email" value="{{ old('email') }}" name="email" class="form-control border-end-0" required>
                                                    <span class="input-group-text border-start-0">
                                                        <i class="ti ti-mail"></i>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Password</label>
                                                <div class="pass-group">
                                                    <input type="password" class="pass-input form-control" name="password" required>
                                                    <span class="ti toggle-password ti-eye-off"></span>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <div class="form-check form-check-md mb-0">
                                                    <input class="form-check-input" id="remember_me" name="remember_me" type="checkbox">
                                                    <label for="remember_me" class="form-check-label mt-0">Remember me</label>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-primary w-100">Sign In</button>
                                        </div>

                                        <div class="mt-5 pb-4 text-center">
                                            <p class="mb-0 text-gray-9">Copyright &copy; 2026 - Neural Info Solution Pvt. Ltd</p>
                                            <p class="mb-0 small text-muted mt-1">
                                                <a href="{{ route('privacy.policy') }}" class="text-primary text-decoration-none" target="_blank">Privacy Policy</a>
                                                <span class="mx-1 text-muted">&bull;</span>
                                                <a href="{{ route('terms.conditions') }}" class="text-primary text-decoration-none" target="_blank">Terms &amp; Conditions</a>
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

    <script src="{{ asset('frontent/assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('frontent/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('frontent/assets/js/script.js') }}"></script>
</body>

</html>
