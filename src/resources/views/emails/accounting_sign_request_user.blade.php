<!DOCTYPE html>
<html>
<head>
    <title>Podpis vyúčtovania pracovnej cesty</title>
    @include('emails.partials.email_styles')
</head>
<body>
<div class="email-container">
    <div class="content">
        Vážený používateľ, <br>
        {{ $messageText }}
    </div>
    <div class="footer">
        S pozdravom,<br>
        Tím Pracovné Cesty
    </div>
</div>
</body>
</html>
