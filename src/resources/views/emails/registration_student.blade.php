<!DOCTYPE html>
<html>
<head>
    <title>Registrácia Študenta</title>
    @include('emails.partials.email_styles')
</head>
<body>
<div class="email-container">
    <div class="header">Vitajte v Pracovných Cestách</div>
    <div class="content">
        Vážený študent, <br>
        Vaša registrácia bola úspešne dokončená.
        {{ $messageText }}
    </div>
    <div class="footer">
        S pozdravom,<br>
        Tím Pracovné Cesty
    </div>
</div>
</body>
</html>
