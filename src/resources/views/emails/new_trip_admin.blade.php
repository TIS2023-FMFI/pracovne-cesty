<!DOCTYPE html>
<html>
<head>
    <title>Nová Cesta</title>
    @include('emails.partials.email_styles')
</head>
<body>
<div class="email-container">
    <div class="header">Nová Cesta Nahlásená</div>
    <div class="content">
        Vážený admin, <br>
        Bola nahlásená nová pracovná cesta.
        {{ $messageText }}
    </div>
    <div class="footer">
        S pozdravom,<br>
        Tím Pracovné Cesty
    </div>
</div>
</body>
</html>
