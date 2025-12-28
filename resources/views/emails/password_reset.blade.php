<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h2>Hello!</h2>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <p>
        <a href="http://localhost:5173/update-password?token={{ $token }}&email={{ $email }}">Reset Password</a>
    </p>
    <p>If you did not request a password reset, no further action is required.</p>
</body>
</html>
