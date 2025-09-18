<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset Code</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 500px; 
            margin: 40px auto; 
            padding: 20px; 
            color: #333; 
            line-height: 1.6;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .code-container { 
            background: #f8f9fa; 
            padding: 30px; 
            text-align: center; 
            border-radius: 10px; 
            margin: 25px 0; 
            border: 2px dashed #007bff; 
        }
        .verification-code { 
            font-size: 42px; 
            font-weight: bold; 
            color: #007bff; 
            letter-spacing: 8px; 
            margin: 10px 0; 
            font-family: 'Courier New', monospace;
        }
        .instruction {
            font-size: 16px;
            color: #6c757d;
            margin: 15px 0;
        }
        .expires {
            color: #dc3545;
            font-weight: bold;
            font-size: 14px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #999;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Jeongol Restaurant</div>
        <p style="margin: 0; color: #6c757d;">Reset Password</p>
    </div>
    
    <p>Enter this verification code.</p>
    
    <div class="code-container">
        <div class="verification-code">{{ $code }}</div>
    </div>
    
</body>
</html>