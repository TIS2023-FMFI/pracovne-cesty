<!DOCTYPE html>
<html>
<head>
    <title>Storno Cesty</title>
    @include('emails.partials.email_styles')
</head>
<body>
<div class="email-container">
    <div class="header">Informácia o Storne Cesty</div>
    <div class="content">
        Vážený používateľ, <br>
        Vaša pracovná cesta bola stornovaná.
        {{ $messageText }}
    </div>
    <div class="footer">
        S pozdravom,<br>
        Tím Pracovné Cesty
    </div>
</div>
</body>
</html>
