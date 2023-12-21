<!DOCTYPE html>
<html>
<head>
    <title>Storno Cesty</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 800px;
            padding: 15px;
            border-radius: 8px;
        }
        .header {
            font-size: 24px;
            color: #444444;
            margin-bottom: 20px;
        }
        .content {
            font-size: 16px;
            color: #555555;
            line-height: 1.6;
            text-align: left;
        }
        .footer {
            max-width: 600px;
            margin-top: 30px;
            font-size: 14px;
            text-align: right;
            color: #999999;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">Informácia o Storne Cesty</div>
    <div class="content">
        Vážený používateľ, <br>
        Vaša cesta bola stornovaná.
        {{ $messageText }}
    </div>
    <div class="footer">
        S pozdravom,<br>
        Tím Pracovné Cesty
    </div>
</div>
</body>
</html>
