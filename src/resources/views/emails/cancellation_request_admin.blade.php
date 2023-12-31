<!DOCTYPE html>
<html>
<head>
    <title>Žiadosť o Storno</title>
    @include('emails.partials.email_styles')
</head>
<body>
<div class="email-container">
    <div class="header">Žiadosť o Storno Cesty</div>
    <div class="content">
        Vážený admin, <br>
        Bola podaná žiadosť o storno cesty. Prosím, skontrolujte a spracujte žiadosť.
        {{ $messageText }}
    </div>
    <div class="footer">
        S pozdravom,<br>
        Tím Pracovné Cesty
    </div>
</div>
</body>
</html>
