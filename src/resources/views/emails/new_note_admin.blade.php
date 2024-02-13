<!DOCTYPE html>
<html>
<head>
    <title>Nová Poznámka pri Ceste</title>
    @include('emails.partials.email_styles')
</head>
<body>
<div class="email-container">
    <div class="header">Nová Poznámka k Ceste</div>
    <div class="content">
        Vážený admin, <br>
        K jednej z ciest bola pridaná nová poznámka.
        {{ $messageText }}
    </div>
    <div class="footer">
        S pozdravom,<br>
        Tím Pracovné Cesty
    </div>
</div>
</body>
</html>
