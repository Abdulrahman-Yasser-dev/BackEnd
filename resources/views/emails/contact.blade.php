<!DOCTYPE html>
<html>

<head>
    <title>New Contact Message</title>
</head>

<body>
    <div dir="rtl" style="text-align: right; font-family: Arial, sans-serif;">
        <h2>رسالة جديدة من نموذج التواصل</h2>
        <p><strong>الاسم:</strong> {{ $name }}</p>
        <p><strong>البريد الإلكتروني:</strong> {{ $email }}</p>
        <p><strong>الرسالة:</strong></p>
        <p style="background-color: #f9f9f9; padding: 10px; border-radius: 5px;">{{ $bodyMessage }}</p>
    </div>
</body>

</html>