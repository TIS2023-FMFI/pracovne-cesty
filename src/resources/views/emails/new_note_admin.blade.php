<!DOCTYPE html>
<html>
<head>
    <title>Nová poznámka pri ceste</title>
    @include('emails.partials.email_styles')
</head>
<body>
<div class="email-container">
    <div class="content">
        Vážený admin, <br>
        k jednej z ciest bola pridaná nová poznámka. <br>
        {!! nl2br(e($messageText)) !!}
    </div>
    <div class="footer">
        S pozdravom,<br>
        Tím Pracovné Cesty
    </div>
</div>
</body>
</html>
