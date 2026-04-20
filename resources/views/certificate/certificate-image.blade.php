<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Votacion</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 12px;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #14263f;
            background: #ffffff;
        }

        .certificate-shell {
            position: relative;
            padding: 10px;
            border: 3px solid #102947;
            background: linear-gradient(180deg, #0d2240 0%, #1a3a6b 100%);
        }

        .certificate-shell::before {
            content: "";
            position: absolute;
            inset: 6px;
            border: 1px solid rgba(255, 255, 255, 0.22);
        }

        .certificate-card {
            position: relative;
            z-index: 1;
            padding: 28px 36px 26px;
            background: linear-gradient(180deg, #ffffff 0%, #f0f5fb 100%);
            border: 1px solid rgba(16, 41, 71, 0.2);
        }

        .top-bar {
            width: 100%;
            height: 8px;
            margin-bottom: 22px;
            background: linear-gradient(90deg, #102947 0%, #1f5298 55%, #4a90d9 100%);
            border-radius: 999px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo-panel {
            margin: 0 auto 18px;
            padding: 20px 24px 16px;
            width: 280px;
            border-radius: 18px;
            background: linear-gradient(135deg, #0d2240 0%, #1a3a6b 60%, #1f5298 100%);
            box-shadow: 0 14px 32px rgba(13, 34, 64, 0.35);
            border: 1px solid rgba(255,255,255,0.12);
        }

        .logo {
            width: 150px;
            height: auto;
            margin-bottom: 10px;
        }

        .university {
            margin: 0;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #4c5a6d;
        }

        .title {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            color: #0d2240;
        }

        .subtitle {
            margin: 8px auto 0;
            max-width: 480px;
            font-size: 12.5px;
            line-height: 1.55;
            color: #4c5a6d;
        }

        .content-panel {
            margin-top: 16px;
            padding: 20px 24px;
            border-radius: 14px;
            background: rgba(240, 245, 251, 0.95);
            border: 1px solid rgba(16, 41, 71, 0.14);
        }

        .section-label {
            text-align: center;
            font-size: 11px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #1f5298;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .voter-name {
            margin: 0 0 8px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            color: #0d2240;
        }

        .voter-id {
            margin: 0;
            text-align: center;
            font-size: 14.5px;
            color: #314155;
        }

        .voter-id strong {
            color: #0d2240;
        }

        .footer {
            margin-top: 20px;
            padding-top: 14px;
            border-top: 2px solid #102947;
            text-align: center;
        }

        .footer-main {
            margin: 0;
            font-size: 13px;
            color: #314155;
        }

        .footer-note {
            margin: 6px 0 0;
            font-size: 11px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #1f5298;
            font-weight: 700;
        }
    </style>
</head>
<body>
@php
    $logoPath = public_path('images/whiteLogo.png');
    $logoData = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;
@endphp

<div class="certificate-shell">
    <div class="certificate-card">
        <div class="top-bar"></div>

        <div class="header">
            <div class="logo-panel">
                @if($logoData)
                    <img class="logo" src="{{ $logoData }}" alt="Logo Universidad de Ibague">
                @endif
                <p class="university">Universidad de Ibague</p>
            </div>

            <h1 class="title">Certificado de Votación</h1>
            <p class="subtitle">
                Documento emitido por el Sistema de Votaciones para certificar la participación
                del votante en el proceso electoral institucional.
            </p>
        </div>

        <div class="content-panel">
            <div class="section-label">Constancia oficial de participación</div>

            <h2 class="voter-name">{{ $voter->name }}</h2>
            <p class="voter-id">
                Identificado(a) con numero de documento <strong>{{ $voter->identification_number }}</strong>
            </p>


        </div>

        <div class="footer">
            <p class="footer-main">
                Gracias por participar con responsabilidad y compromiso democratico.
            </p>
            <p class="footer-note">Sistema de Votaciones • Secretaria General</p>
        </div>
    </div>
</div>
</body>
</html>
