<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Votación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 24px;
        }
        .subtitle {
            font-size: 18px;
            margin-bottom: 12px;
        }
        .highlight {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .divider {
            border-top: 1px solid #ddd;
            margin: 32px 0;
        }
        .footer {
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="title">Certificado de Votación</div>
        <div class="subtitle">Este certificado valida que:</div>
        <div class="highlight">{{ $voter->name }}</div>
        <div class="subtitle">
            con número de identificación <strong>{{ $voter->identification_number }}</strong>
        </div>
        <div class="subtitle">ejerció su derecho al voto el día:</div>
        <div class="highlight">{{ $voted_at }}</div>
        <div class="divider"></div>
        <div class="footer">
            Gracias por participar en el proceso electoral de la Universidad de Ibagué.
        </div>
    </div>
</div>
</body>
</html>
