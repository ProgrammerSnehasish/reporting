<?php
//session_start();
require_once "../config/config.php";
?>
<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Login | LUMINIA LIFECARE PVT. LTD.</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Employee Login Portal" name="description" />
    <meta content="Themesdesign" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="../assets/img/favicon.jpeg">

    <!-- Bootstrap Css -->
    <link href="../assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="../assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />

    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body.auth-body-bg {
            margin: 0;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #eef1f4;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-shell {
            width: 100%;
            max-width: 380px;
            min-height: 100vh;
            background: #fff;
            margin: 0 auto;
            box-shadow: 0 0 40px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        @media (min-width: 480px) {
            .login-shell {
                min-height: 700px;
                margin: 24px auto;
                border-radius: 22px;
            }
        }

        .login-hero {
            position: relative;
            background: linear-gradient(160deg, #0f4c46 0%, #12695f 45%, #17a589 100%);
            padding: 48px 28px 70px;
            text-align: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .login-hero::before,
        .login-hero::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }

        .login-hero::before {
            width: 180px;
            height: 180px;
            top: -60px;
            left: -50px;
        }

        .login-hero::after {
            width: 140px;
            height: 140px;
            bottom: -40px;
            right: -40px;
        }

        .hero-icons {
            display: flex;
            justify-content: center;
            gap: 22px;
            margin-bottom: 14px;
            position: relative;
            z-index: 1;
        }

        .hero-icons span {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(255,255,255,0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
        }

        .hero-tag {
            color: #ffcf4d;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            position: relative;
            z-index: 1;
            margin-bottom: 6px;
        }

        .hero-title {
            color: #ffffff;
            font-weight: 800;
            font-size: 26px;
            letter-spacing: 1px;
            margin: 0 0 18px;
            position: relative;
            z-index: 1;
        }

        .hero-sub {
            color: #ffe14d;
            font-weight: 700;
            font-size: 19px;
            margin: 0 0 6px;
            position: relative;
            z-index: 1;
        }

        .hero-desc {
            color: rgba(255,255,255,0.85);
            font-size: 13px;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .login-card {
            background: #fff;
            margin-top: -34px;
            border-radius: 26px 26px 0 0;
            padding: 34px 26px 26px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .field-group {
            position: relative;
            margin-bottom: 18px;
        }

        .field-group input {
            width: 100%;
            padding: 15px 18px;
            border-radius: 14px;
            border: 1px solid #e2e5ea;
            background: #f7f8fa;
            font-size: 14.5px;
            color: #333;
            outline: none;
            transition: border-color .15s ease, background .15s ease;
        }

        .field-group input::placeholder {
            color: #9aa1ab;
        }

        .field-group input:focus {
            border-color: #17a589;
            background: #fff;
        }

        .field-group .toggle-eye {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
            font-size: 18px;
            background: none;
            border: none;
            padding: 0;
            line-height: 1;
        }

        .btn-login {
            width: 100%;
            border: none;
            padding: 15px;
            border-radius: 14px;
            background: linear-gradient(90deg, #0f4c46, #17a589);
            color: #fff;
            font-weight: 600;
            font-size: 16px;
            letter-spacing: .3px;
            cursor: pointer;
            transition: opacity .15s ease;
            margin-top: 6px;
        }

        .btn-login:hover {
            opacity: 0.92;
        }

        .forgot-link {
            text-align: center;
            margin-top: 18px;
        }

        .forgot-link a {
            color: #6b7280;
            font-size: 13.5px;
            text-decoration: none;
        }

        .forgot-link a:hover {
            color: #17a589;
            text-decoration: underline;
        }

        .login-footer {
            margin-top: auto;
            text-align: center;
            padding-top: 30px;
        }

        .login-footer .privacy a {
            color: #17a589;
            font-size: 13px;
            text-decoration: underline;
        }

        .login-footer .copyright {
            margin-top: 8px;
            font-size: 12px;
            color: #8a8f98;
        }

        .login-footer .copyright b {
            color: #4b5158;
        }

        .alert-danger {
            border-radius: 12px;
            font-size: 13.5px;
        }
    </style>

</head>

<body class="auth-body-bg">

    <div class="login-shell">

        <div class="login-hero">
            <div class="hero-icons">
                <span><i class="mdi mdi-chart-bar"></i></span>
                <span><i class="mdi mdi-plus"></i></span>
                <span><i class="mdi mdi-cog-outline"></i></span>
            </div>
            <div class="hero-tag">Employee Portal</div>
            <h1 class="hero-title">LUMINIA LIFECARE</h1>
            <p class="hero-sub">Sign in to continue</p>
            <p class="hero-desc">Access your dashboard and daily activity, in one place</p>
        </div>

        <div class="login-card mt-1">

            <?php if (isset($_SESSION['error'])) { ?>

                <div class="alert alert-danger alert-dismissible fade show">

                    <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                </div>

            <?php } ?>

            <form method="POST" action="login-process.php">

                <div class="field-group">
                    <input
                        type="text"
                        name="username"
                        placeholder="Enter Username"
                        required>
                </div>

                <div class="field-group">
                    <input
                        id="passwordField"
                        type="password"
                        name="password"
                        placeholder="Enter Password"
                        required>
                    <button type="button" class="toggle-eye" onclick="togglePassword()">
                        <i id="eyeIcon" class="mdi mdi-eye-outline"></i>
                    </button>
                </div>

                <button class="btn-login" type="submit">Login</button>

                <div class="forgot-link">
                    <a href="#"><i class="mdi mdi-lock-outline"></i> Forgot your password?</a>
                </div>

            </form>

            <div class="login-footer">
                <div class="privacy">
                    <a href="#">Privacy Policy</a>
                </div>
                <div class="copyright">
                    &copy; <?php echo date('Y'); ?> <b>Luminia Lifecare Pvt. Ltd.</b>
                </div>
            </div>

        </div>

    </div>

    <!-- JAVASCRIPT -->
    <script src="../assets/libs/jquery/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="../assets/libs/simplebar/simplebar.min.js"></script>
    <script src="../assets/libs/node-waves/waves.min.js"></script>

    <script src="../assets/js/app.js"></script>

    <script>
        function togglePassword() {
            const field = document.getElementById('passwordField');
            const icon = document.getElementById('eyeIcon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('mdi-eye-outline');
                icon.classList.add('mdi-eye-off-outline');
            } else {
                field.type = 'password';
                icon.classList.remove('mdi-eye-off-outline');
                icon.classList.add('mdi-eye-outline');
            }
        }
    </script>

</body>

</html>