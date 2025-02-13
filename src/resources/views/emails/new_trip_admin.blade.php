<!DOCTYPE html>
<html>
<head>
    <title>Nová cesta</title>
    @include('emails.partials.email_styles')
</head>
<body>
<div class="email-container">
    <div class="content">
        Vážený admin, <br>
        bola pridaná nová pracovná cesta. <br>
        {!! nl2br(e($messageText)) !!}
    </div>
    <div class="footer">
        S pozdravom,<br>
        Tím Pracovné Cesty
    </div>
</div>
</body>
</html>
