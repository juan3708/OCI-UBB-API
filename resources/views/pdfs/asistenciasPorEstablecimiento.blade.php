<!DOCTYPE html>
<html>

<head>
    <title>
    </title>
    <meta content="summary_large_image" name="twitter:card">
    <meta content="website" property="og:type">
    <meta content property="og:description">
    <meta content property="og:title">
    <meta content name="description">
    <meta charset="utf-8">
    <meta content="width=device-width" name="viewport">
</head>

<body style="background-color: #fff;color: #000;font-family: Arial, " Helvetica Neue", Helvetica, sans-serif">
    <div>
        <div style="border-bottom: 2px solid #000;">
            <div style="max-width: 1440px;margin: 0 auto;">
                <div>
                    <img alt src="storage/images/descarga-removebg-preview-1093504684_1655867875.png"
                        style="max-width:115px;display: block;width: 100%;margin: 0 auto;float: left">
                    <img alt src="storage/images/Escudo_monocroma_2-104461428_1655867876.png"
                        style="max-width:90px;display: block;width: 100%;margin: 0 auto-;float: right">

                    <h3
                        style="color:#000000;font-size:20px;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;line-height:150%;text-align:center;direction:ltr;font-weight:700;letter-spacing:normal;margin-top:0;margin-bottom:0;">
                        <span class="tinyMce-placeholder">
                            Universidad del B&iacute;o - B&iacute;o <br> Olimpiadas Chilenas de Inform&aacute;tica
                        </span>
                    </h3>

                </div>
            </div>
        </div>
    </div>
    <div>
        <div>
            <h2
                style="color:#000000;font-size:35px;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;line-height:150%;text-align:center;direction:ltr;font-weight:700;letter-spacing:normal;margin-top:10;margin-bottom:10;">
                <span class="tinyMce-placeholder">
                    {{ $nombreCiclo }}
                    <br>
                    Informe de asistencia del establecimiento


                </span>
            </h2>
        </div>
    </div>
    <div>
        <h3 style="font-weight: bold;text-align: left;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
            INFORMACION DEL ESTABLECIMIENTO</h3>
        <table
            style="width: 100%;border-collapse: collapse;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif; ">
            <tr style="text-align: left;">
                <td>Nombre: {{ $nombreEstablecimiento }}</td>

                <td>Email Contacto: {{ $email }}</td>
            </tr>
            <tr>
                <td>Email Profesor: {{ $emailProfesor }}</td>
                <td>Cantidad de estudiantes: {{ $cantEstudiantes }}</td>

            </tr>
        </table>
    </div>
    <div>

        <div>
            <table
                style="width: 100%;border: 1px solid #999;border-collapse: collapse;text-align: left;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif; ">
                <caption>
                    <h3
                        style="font-weight: bold;text-align: center;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                        ESTUDIANTES</h3>
                </caption>
                <thead>
                    <tr style="text-align: center;background-color: #f6f6f6;">
                        <th style="border: 1px solid #999;">RUT</th>
                        <th style="border: 1px solid #999;">NOMBRE</th>
                        <th style="border: 1px solid #999;">APELLIDOS</th>
                        <th style="border: 1px solid #999;">PORCENTAJE ASISTENCIA</th>
                        <th style="border: 1px solid #999;">ASISTENCIA</th>
                        <th style="border: 1px solid #999;">INASISTENCIA</th>
                        <th style="border: 1px solid #999;">TOTAL CLASES</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < $cantEstudiantes; $i++)
                        <tr>
                            <td style="border: 1px solid #999;text-align: center;width: 80px;">
                                {{ $students[$i]['rut'] }}</td>
                            <td style="border: 1px solid #999;text-align: center;">{{ $students[$i]['nombre'] }}</td>
                            <td style="border: 1px solid #999;text-align: center;">{{ $students[$i]['apellidos'] }}</td>
                            <td style="border: 1px solid #999;text-align: center;width: 50px;">
                                {{ $students[$i]['PorcentajeAsistencia'] }}%
                            </td>
                            <td style="border: 1px solid #999;text-align: center;">
                                {{ $students[$i]['CantAsistenciasEInasistencias'][0]['asistencias'] }}</td>
                            <td style="border: 1px solid #999;text-align: center;">
                                {{ $students[$i]['CantAsistenciasEInasistencias'][0]['inasistencias'] }}</td>
                            <td style="border: 1px solid #999;text-align: center;">
                                {{ count($students[$i]['Asistencias']) }}
                            </td>

                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
    <h6 style="float: right">Fecha de emisión: {{ $fechaEmision }}</h6>
</body>

</html>
