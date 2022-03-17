<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid black;
            text-align: left;
            padding: 5px;
        }

        th {
            text-align: center;
            background-color: #0f1f39;
            color: #ffffff;
        }
    </style>
</head>
<body>
<div>
    <h1 style="text-align: center">Resultados de votación para la {{$tableName}}</h1>
    @if(count($votes) !==0)

        <table>
            <thead>
            <tr>
                <th>
                    Opción de votacion
                </th>
                <th>
                    Nombre candidato principal
                </th>
                <th>
                    Nombre candidato suplente
                </th>
                <th>
                    Cantidad votos
                </th>
            </tr>
            </thead>

            <tbody>
            @foreach($votes as $vote)
                <tr>

                    <td>
                        {{$vote->voting_option}}
                    </td>
                    <!-- If there is no candidate principal name, is because is empty vote-->
                    @if($vote->principal_name === null)
                        <td>
                            Voto en blanco
                        </td>

                        <td>
                            Voto en blanco
                        </td>
                    @else
                    <!--There is candidate register, so, and empty substitute_name would mean there is no substitute
                        candidate registered-->
                        <td>
                            {{$vote->principal_name}}
                        </td>

                        <td>
                            {{$vote->substitute_name?? 'Sin candidato'}}
                        </td>
                    @endif

                    <td>
                        {{$vote->total_votes}}
                    </td>

                </tr>

            @endforeach
            </tbody>
        </table>
    @else
        <h2 style="text-align: center">
            No hay votos registrados en esta mesa de votación
        </h2>

    @endif

    <p>
        Este reporte ha sido generado por el Sistema de Votaciones el {{new \Carbon\Carbon()}}.
    </p>
</div>


</body>
</html>
