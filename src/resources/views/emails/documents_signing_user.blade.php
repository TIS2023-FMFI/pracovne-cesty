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
        Prosím, skontrolujte si vygenerované dokumenty pri vašej pracovnej cesty, podľa potreby si dokumenty vytlačte a doručte podpísané na sekretariát alebo sa dostavte podpísať dokumenty na sekretariát osobne.
        {{ $messageText }}
    </div>
    <div class="footer">
        S pozdravom,<br>
        Tím Pracovné Cesty
    </div>
</div>
</body>
</html>
