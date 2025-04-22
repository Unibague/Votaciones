<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado de votación</title>
</head>
<body>
    <h2>¡Gracias por votar, {{ $name }}!</h2>

    <p>
        Este correo es tu comprobante de participación en el proceso electoral. 
    </p>

    <p style="margin-top: 30px;">Fecha de emisión: {{ now()->format('d/m/Y H:i') }}</p>

    @if($cid)
        <img src="cid:{{ $cid }}" alt="Certificado de votación" style="margin-top:20px; max-width: 100%;">
    @else
        <p style="color:red">⚠ No se pudo incrustar la imagen.</p>
    @endif
</body>
</html>
