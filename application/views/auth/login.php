<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Simpeldar - Login</title>

    <link rel="shortcut icon"
          href="<?php echo base_url('assets/logo/bld.png'); ?>">

    <!-- Font -->
    <link rel="stylesheet"
          href="<?php echo base_url('assets/login_style/css/font.css'); ?>">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="<?php echo base_url('assets/login_style/css/font-awesome.min.css'); ?>">

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100%;
            font-family: "Merriweather", sans-serif;
        }

        body {
            background: url("<?php echo base_url('assets/admin/img/bg.png'); ?>")
                no-repeat center center fixed;

            background-size: cover;
            position: relative;
        }

        /* Overlay background */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;

            background: rgba(0, 0, 0, 0.60);

            z-index: 0;
        }

        /* Wrapper */
        .login-wrapper {
            position: relative;
            z-index: 1;

            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 30px 20px;
        }

        /* Login Card */
        .login-card {
            width: 100%;
            max-width: 420px;

            background: #ffffff;

            padding: 38px 35px 32px;

            border-radius: 15px;

            box-shadow:
                0 10px 35px rgba(0, 0, 0, 0.35);
        }

        /* Header */
        .login-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .login-logo {
            width: 75px;
            height: 75px;

            object-fit: contain;

            margin-bottom: 15px;
        }

        .login-header h2 {
            margin: 0;

            font-size: 22px;
            font-weight: 700;

            color: #333;
        }

        .login-header p {
            margin: 7px 0 0;

            font-size: 13px;
            color: #777;
        }

        /* Alert */
        .alert {
            padding: 12px 14px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-size: 13px;
            line-height: 1.5;
        }

        .alert-danger {
            color: #842029;
            background-color: #f8d7da;
            border: 1px solid #f5c2c7;
        }

        .alert-success {
            color: #0f5132;
            background-color: #d1e7dd;
            border: 1px solid #badbcc;
        }

        .alert-warning {
            color: #664d03;
            background-color: #fff3cd;
            border: 1px solid #ffecb5;
        }

        /* Input Group */
        .input-group {
            position: relative;
            margin-bottom: 18px;
        }

        /* Icon kiri */
        .input-group i.input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
            font-size: 15px;
            z-index: 2;
        }

        /* Input */
        .input-group input {
            width: 100%;
            height: 48px;
            padding: 0 45px 0 43px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            font-family: "Merriweather", sans-serif;
            font-size: 14px;
            color: #333;
            transition: all 0.2s ease;
        }

        .input-group input:focus {
            border-color: #00B2BD;
            box-shadow:
                0 0 0 3px rgba(0, 178, 189, 0.12);
        }

        .input-group input::placeholder {
            color: #aaa;
        }

        /* Show Password */
        .show-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            cursor: pointer;
            font-size: 15px;
            z-index: 2;
        }

        .show-password:hover {
            color: #00B2BD;
        }

        /* Login Button */
        .login-button {
            width: 100%;
            height: 48px;
            border: none;
            border-radius: 8px;
            background-color: #00B2BD;
            color: #fff;
            font-family: "Merriweather", sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .login-button:hover {
            background-color: #1DD1DE;
            box-shadow:
                0 4px 12px rgba(0, 178, 189, 0.25);
        }

        .login-button:active {
            transform: translateY(1px);
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 25px;
            font-size: 12px;
            color: #999;
        }

        /* Responsive */
        @media (max-width: 480px) {

            .login-wrapper {
                padding: 20px 15px;
            }

            .login-card {
                padding: 30px 22px 25px;

                border-radius: 12px;
            }

            .login-header h2 {
                font-size: 20px;
            }

            .login-header p {
                font-size: 12px;
            }
        }
    </style>
</head>


<body>

    <div class="login-wrapper">

        <div class="login-card">

            <!-- Header -->
            <div class="login-header">

                <img
                    src="<?php echo base_url('assets/logo/bld.png'); ?>"
                    alt="Logo"
                    class="login-logo">

                <h2>Aplikasi Pelayanan Darah</h2>

            </div>

            <!-- Login Form -->
            <form
                action="<?php echo base_url('index.php/auth/login'); ?>"
                method="post"
                class="login-form">

                <!-- CSRF -->
                <input
                    type="hidden"
                    name="<?= $this->security->get_csrf_token_name(); ?>"
                    value="<?= $this->security->get_csrf_hash(); ?>">


                <!-- Username -->
                <div class="input-group">

                    <i class="fa fa-user input-icon"></i>

                    <input
                        type="text"
                        class="input"
                        name="uname"
                        placeholder="Username"
                        autocomplete="username"
                        required>

                </div>


                <!-- Password -->
                <div class="input-group">

                    <i class="fa fa-lock input-icon"></i>

                    <input
                        type="password"
                        id="login-password"
                        class="input"
                        name="pass"
                        placeholder="Password"
                        autocomplete="current-password"
                        required>

                    <i
                        id="show-password"
                        class="fa fa-eye show-password"
                        title="Tampilkan password">
                    </i>

                </div>


                <!-- Login Button -->
                <button
                    type="submit"
                    name="login"
                    class="login-button">

                    <i class="fa fa-sign-in"></i>
                    Login

                </button>

            </form>

        </div>

    </div>


    <!-- Show / Hide Password -->
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const passwordInput =
                document.getElementById('login-password');

            const showPassword =
                document.getElementById('show-password');


            showPassword.addEventListener('click', function () {

                if (passwordInput.type === 'password') {

                    passwordInput.type = 'text';

                    showPassword.classList.remove('fa-eye');

                    showPassword.classList.add('fa-eye-slash');

                    showPassword.setAttribute(
                        'title',
                        'Sembunyikan password'
                    );

                } else {

                    passwordInput.type = 'password';

                    showPassword.classList.remove('fa-eye-slash');

                    showPassword.classList.add('fa-eye');

                    showPassword.setAttribute(
                        'title',
                        'Tampilkan password'
                    );
                }

            });

        });

    </script>

</body>
</html>