<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue</title>
    <style>
        body { font-family: Georgia, serif; background: #F5F5DC; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; }
        .header { background: #D4AF37; color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; }
        .btn { display: inline-block; padding: 12px 24px; background: #D4AF37; color: white; text-decoration: none; border-radius: 4px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✨ Bienvenue {{ $user->name }}</h1>
        </div>
        
        <div class="content">
            <p>Merci de rejoindre la famille <strong>Les bougies de Séraphie</strong>.</p>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/catalogue') }}" class="btn">Découvrir nos bougies</a>
            </p>
        </div>
    </div>
</body>
</html>
