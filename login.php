<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Byte & Beyond</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6c63ff;
            --secondary-color: #4d44db;
            --dark-color: #2a2a72;
            --light-color: #f8f9fa;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7ff;
            display: flex;
            min-height: 100vh;
            align-items: center;
        }

        .login-container {
            max-width: 450px;
            width: 100%;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin: 20px auto;
        }

        .login-header {
            background-color: var(--primary-color);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .login-header h2 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .login-header p {
            opacity: 0.9;
            margin-bottom: 0;
        }

        .login-body {
            padding: 30px;
        }

        .form-control {
            height: 50px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding-left: 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(108, 99, 255, 0.25);
        }

        .form-label {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .btn-login {
            background-color: var(--primary-color);
            border: none;
            height: 50px;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .input-group-text {
            background-color: transparent;
            border-right: none;
            border-radius: 8px 0 0 8px !important;
        }

        .input-with-icon {
            border-left: none;
            border-radius: 0 8px 8px 0 !important;
        }

        .forgot-password {
            text-align: right;
            margin-top: -10px;
            margin-bottom: 20px;
        }

        .forgot-password a {
            color: #666;
            font-size: 0.9rem;
            text-decoration: none;
            transition: color 0.3s;
        }

        .forgot-password a:hover {
            color: var(--primary-color);
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .register-link a {
            color: var(--primary-color);
            font-weight: 500;
            text-decoration: none;
            transition: color 0.3s;
        }

        .register-link a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .brand-logo {
            font-weight: 700;
            color: white;
            font-size: 1.8rem;
            margin-bottom: 15px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="brand-logo">Byte & Beyond</div>
            <h2>Welcome Back</h2>
            <p>Sign in to continue to your account</p>
        </div>

        <div class="login-body">
            <form action="login_process.php" method="POST">
                <div class="mb-4">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control input-with-icon" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control input-with-icon" id="password" name="password" placeholder="Enter your password" required>
                    </div>

                </div>

                <button type="submit" class="btn btn-primary btn-login btn-block mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i> Login
                </button>

                <div class="register-link">
                    Don't have an account? <a href="registration.html">Create account</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>