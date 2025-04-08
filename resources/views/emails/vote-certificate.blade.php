<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado de votación</title>
</head>
<body>
    <h2>¡Gracias por votar, {{ $name }}!</h2>

    <p>
        Este correo es tu comprobante de participación en el proceso electoral. El código QR se adjunta en este correo como imagen.
    </p>

    <p>
        También puedes hacer clic en este enlace para verificar su validez:<br>
        <a href="{{ $certificateUrl }}">{{ $certificateUrl }}</a>
    </p>

    <p style="margin-top: 30px;">Fecha de emisión: {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>
