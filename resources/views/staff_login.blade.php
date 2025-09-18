<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
    <title>Login Page</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .login-box {
            background-color: #fff;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            width: 100%;
            max-width: 420px;
            text-align: center;
            position: relative;
        }

        .staff-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background: linear-gradient(45deg, #00b894, #00a085);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0, 184, 148, 0.4);
        }

        .login-box img {
            max-width: 100px;
            height: auto;
            margin-bottom: 1rem;
            border-radius: 50%;
            border: 4px solid #f8f9fa;
        }

        .login-title {
            color: #2c3e50;
            margin-bottom: 2rem;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .role-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            font-size: 0.9rem;
            color: #1565c0;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 600;
            font-size: 0.9rem;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e9ecef;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #74b9ff;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(116, 185, 255, 0.1);
        }

        .error-message {
            color: #e74c3c;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-align: left;
            background-color: #fdf2f2;
            padding: 0.75rem;
            border-radius: 0.5rem;
            border-left: 4px solid #e74c3c;
        }

        .success-message {
            color: #27ae60;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-align: center;
            background-color: #f0f9f4;
            padding: 0.75rem;
            border-radius: 0.5rem;
            border-left: 4px solid #27ae60;
        }

        .btn {
            width: 100%;
            padding: 0.875rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(116, 185, 255, 0.3);
        }

        .admin-link {
            color: #6c757d;
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            margin-top: 1rem;
        }

        .admin-link:hover {
            color: #74b9ff;
        }

        .help-text {
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 1rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            text-align: left;
        }

        @media (max-width: 500px) {
            .login-box {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }

            .login-box img {
                max-width: 80px;
            }


            .login-title {
                font-size: 1.5rem;
            }

            .staff-badge {
                font-size: 0.7rem;
                padding: 0.4rem 0.8rem;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="login-box">
            <img src="{{ asset('logo/jeongol_logo.jpg') }}" alt="Jeongol Logo">

            <h2 class="login-title">Login</h2>

            @if(session('error'))
                <div class="error-message">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="error-message">
                    <ul style="margin: 0; padding-left: 1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('status'))
                <div class="success-message">{{ session('status') }}</div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>

    <script>
        setTimeout(function () {
            const messages = document.querySelectorAll('.success-message, .error-message');
            messages.forEach(function (message) {
                if (message.style.display !== 'none') {
                    message.style.opacity = '0';
                    message.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => message.style.display = 'none', 500);
                }
            });
        }, 5000);

       document.addEventListener('DOMContentLoaded', function() {
    history.pushState(null, null, location.href);
    
    window.onpopstate = function(event) {
        history.go(1);
    };
});
    </script>

</body>

</html>