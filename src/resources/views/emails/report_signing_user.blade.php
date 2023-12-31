<!DOCTYPE html>
<html>
<head>
    <title>Podpísanie Správy</title>
    @include('emails.partials.email_styles')
</head>
<body>
<div class="email-container">
    <div class="header">Podpísanie Správy</div>
    <div class="content">
        Vážený používateľ, <br>
        Prosím, nezabudnite podpísať správu po vašej ceste.
        {{ $messageText }}
    </div>
    <div class="footer">
        S pozdravom,<br>
        Tím Pracovné Cesty
    </div>
</div>
</body>
</html>
