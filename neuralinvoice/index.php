<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!empty($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit;
}
include("EvaraFroentend/layout/head.php");
// include("EvaraFroentend\layout\header.php");
?>
<style>
    body {
        margin: 0;
        padding: 0;
        height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);
    }

    .login-section {
        min-height: 90vh;
        display: flex;
        align-items: center;
    }

    .login-logo {
        text-align: center;
        margin-bottom: 30px;
    }

    .login-logo img {
        width: 130px;
    }

    .login_wrap {
        max-width: 420px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid #eef0f3;
        padding: 40px 35px;
    }

    .login_wrap .heading_s1 h3 {
        font-weight: 700;
        margin-bottom: 5px;
    }

    .login_wrap .heading_sub {
        color: #8a8f98;
        font-size: 14px;
        margin-bottom: 25px;
    }

    .login_wrap .form-group {
        margin-bottom: 18px;
    }

    .login_wrap .form-control {
        height: 48px;
        border-radius: 8px;
        border: 1px solid #dfe3e8;
        padding: 10px 15px;
        font-size: 14px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .login_wrap .form-control:focus {
        border-color: #17a2b8;
        box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.15);
        outline: none;
    }

    .login_footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px !important;
    }

    .login_footer a {
        font-size: 13px;
        color: #17a2b8;
        text-decoration: none;
    }

    .login_footer a:hover {
        text-decoration: underline;
    }

    .btn-fill-out {
        height: 48px;
        border-radius: 8px;
        font-weight: 600;
        letter-spacing: 0.3px;
        background-color: #17a2b8;
        border: none;
        transition: background-color 0.2s ease, transform 0.15s ease;
    }

    .btn-fill-out:hover {
        background-color: #138a9c;
        transform: translateY(-1px);
    }

    .register-link {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
        color: #6c757d;
    }

    .register-link a {
        color: #17a2b8;
        font-weight: 600;
        text-decoration: none;
    }

    .register-link a:hover {
        text-decoration: underline;
    }
</style>

<main class="main">
    <section class="login-section">
        <div class="container">
            <div class="login-logo">
                <a href="neural-logo-redesign.png">
                    <img src="neural-logo-redesign.png" alt="Neural Logo Redesign">
                </a>
            </div>

            <div class="login_wrap">
                <div class="heading_s1">
                    <h3>Login</h3>
                    <p class="heading_sub">Welcome back! Please enter your details.</p>
                </div>
                <?php if (!empty($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars((string) $_GET['error'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form id="login-form" class="form" action="login.php" method="POST">
                    <div class="form-group">
                        <label for="username" class="mb-1" style="font-size:13px; font-weight:600;">Email address</label>
                        <input placeholder="Enter email address" type="text" name="username" id="username" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="mb-1" style="font-size:13px; font-weight:600;">Password</label>
                        <input placeholder="Enter password" type="password" name="password" id="password" class="form-control" required>
                    </div>

                    <div class="login_footer form-group">
                        <div class="chek-form">
                            <div class="custome-checkbox">
                                <input class="form-check-input" type="checkbox" name="checkbox" id="rememberMe" value="1">
                                <label class="form-check-label" for="rememberMe"><span style="font-size:13px;">Remember me</span></label>
                            </div>
                        </div>
                        <a href="#">Forgot password?</a>
                    </div>

                    <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-fill-out btn-block hover-up">Log in</button>
                    </div>
                </form>

                <div class="register-link">
                    Don't have an account? <a href="register.php">Sign up</a>
                </div>
            </div>
        </div>
    </section>
</main>
<?php include("EvaraFroentend/layout/footer.php"); ?>
