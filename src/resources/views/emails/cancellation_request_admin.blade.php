<!DOCTYPE html>
<html>
<head>
    <title>Žiadosť o storno</title>
    @include('emails.partials.email_styles')
</head>
<body>
<div class="email-container">
    <div class="content">
        Vážený admin, <br>
        bola podaná žiadosť o storno cesty. Prosím, skontrolujte a spracujte žiadosť. <br>
        {!! nl2br(e($messageText)) !!}
    </div>
    <div class="footer">
        S pozdravom,<br>
        Tím Pracovné Cesty
    </div>
</div>
</body>
</html>
