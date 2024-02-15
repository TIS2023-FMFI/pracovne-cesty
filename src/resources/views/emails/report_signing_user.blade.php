<!DOCTYPE html>
<html>
<head>
    <title>Podpísanie správy</title>
    @include('emails.partials.email_styles')
</head>
<body>
<div class="email-container">
    <div class="content">
        Vážený používateľ, <br>
        zastavte sa na sekretariáte podpísať správu o pracovnej ceste alebo si ju vygenerujte v Pracovných cestách a doručte už podpísanú.
        {{ $messageText }}
    </div>
    <div class="footer">
        S pozdravom,<br>
        Tím Pracovné Cesty
    </div>
</div>
</body>
</html>
