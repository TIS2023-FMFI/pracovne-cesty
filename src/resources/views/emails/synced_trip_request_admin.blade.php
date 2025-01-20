<!DOCTYPE html>
<html>
<head>
    <title>Žiadosť o schválenie neprítomnosti</title>
    @include('emails.partials.email_styles')
</head>
<body>
<div class="email-container">
    <div class="content">
        {!! nl2br(e($messageText)) !!}
    </div>
</div>
</body>
</html>
