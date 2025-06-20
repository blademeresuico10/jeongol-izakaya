<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="x-icon" href="logo/jeongol_logo.jpg">
    <title>Login Page</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
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
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-box img {
            max-width: 100px;
            height: auto;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 0.4rem;
            color: #111;
            font-weight: 600;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #cbd5e0;
            border-radius: 4px;
            font-size: 1rem;
        }

        .error-message {
            color: red;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-align: left;
        }

        .status-message {
            color: green;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background-color: #e3342f;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }

        button:hover {
            background-color: #cc1f1a;
        }

        @media (max-width: 500px) {
            .login-box {
                padding: 1.5rem 1rem;
            }

            .login-box img {
                max-width: 80px;
                margin-bottom: 0.8rem;
            }

            label {
                font-size: 0.9rem;
            }

            input {
                font-size: 0.95rem;
            }

            button {
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-box">
            <img src="{{ asset('logo/jeongol_logo.jpg') }}" alt="Jeongol Logo">

            {{-- Display session error --}}
            @if(session('error'))
                <div class="error-message">{{ session('error') }}</div>
            @endif

            {{-- Display validation errors --}}
            @if ($errors->any())
                <div class="error-message">
                    <ul style="margin: 0; padding-left: 1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
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

                <button type="submit">Login</button>
            </form>
        </div>
    </div>

</body>
</html>
