<!DOCTYPE html>
<html>
<head>
    <title>Podpísanie Dokumentov Pred Cestou</title>
    @include('emails.partials.email_styles')
</head>
<body>
<div class="email-container">
    <div class="header">Podpísanie Dokumentov</div>
    <div class="content">
        Vážený používateľ, <br>
        Prosím, nezabudnite podpísať potrebné dokumenty pred vašou cestou.
        {{ $messageText }}
    </div>
    <div class="footer">
        S pozdravom,<br>
        Tím Pracovné Cesty
    </div>
</div>
</body>
</html>
