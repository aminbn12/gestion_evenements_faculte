<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $alert->subject }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #1a3c5e 0%, #0d2137 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.8;
        }
        .content {
            padding: 30px;
        }
        .event-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .event-info h3 {
            margin-top: 0;
            color: #1a3c5e;
        }
        .event-info p {
            margin: 5px 0;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-priority-high {
            background-color: #dc3545;
            color: white;
        }
        .badge-priority-medium {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-priority-low {
            background-color: #28a745;
            color: white;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #c8a951;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 {{ $alert->subject }}</h1>
            <p>Faculté - Gestion Événements</p>
        </div>
        
        <div class="content">
            <p>Bonjour {{ $recipient->first_name }},</p>
            
            <p>{{ $alert->message }}</p>
            
            @if($event)
            <div class="event-info">
                <h3>📅 {{ $event->title }}</h3>
                <p><strong>📍 Lieu:</strong> {{ $event->location ?? 'Non défini' }}</p>
                <p><strong>🗓️ Date:</strong> {{ $event->start_date->format('d/m/Y') }}</p>
                <p><strong>⏰ Heure:</strong> {{ $event->start_date->format('H:i') }}</p>
                @if($event->end_date)
                <p><strong>⏱️ Durée:</strong> Jusqu'au {{ $event->end_date->format('d/m/Y H:i') }}</p>
                @endif
                <p>
                    <strong>⚡ Priorité:</strong> 
                    <span class="badge badge-priority-{{ $event->priority }}">
                        {{ ucfirst($event->priority) }}
                    </span>
                </p>
            </div>
            
            <center>
                <a href="{{ route('events.show', $event) }}" class="btn">
                    Voir l'événement
                </a>
            </center>
            @endif
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} Université Mohammed VI des Sciences de la Santé</p>
            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>
