<!DOCTYPE html>
<html>
<head>
    <title>Registrácia externistu</title>
    @include('emails.partials.email_styles')
</head>
<body>
<div class="email-container">
    <div class="content">
        Dobrý deň, <br>
        boli ste pridaný do systému Pracovné cesty Katedry aplikovanej informatiky FMFI.
        {{ $messageText }}
    </div>
    <div class="footer">
        S pozdravom,<br>
        Tím Pracovné Cesty
    </div>
</div>
</body>
</html>
