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
        Prosím, doručte podpísanú správu o pracovnej ceste na sekretariát alebo sa dostavte podpísať dokument o správe z pracovnej cesty na sekretariát.
        {{ $messageText }}
    </div>
    <div class="footer">
        S pozdravom,<br>
        Tím Pracovné Cesty
    </div>
</div>
</body>
</html>
