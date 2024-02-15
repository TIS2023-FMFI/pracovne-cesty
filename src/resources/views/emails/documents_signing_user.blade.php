<!DOCTYPE html>
<html>
<head>
    <title>Podpísanie dokumentov pred cestou</title>
    @include('emails.partials.email_styles')
</head>
<body>
<div class="email-container">
    <div class="content">
        Vážený používateľ, <br>
        ak Vám boli pri pracovnej ceste vygenerované dokumenty, zastavte sa na sekretariáte podpísať ich, alebo si ich vytlačte a doručte už podpísané.
        {{ $messageText }}
    </div>
    <div class="footer">
        S pozdravom,<br>
        Tím Pracovné Cesty
    </div>
</div>
</body>
</html>
