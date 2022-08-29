<!DOCTYPE html>
<html>

<head>
    <meta content="summary_large_image" name="twitter:card">
    <meta content="website" property="og:type">
    <meta content property="og:description">
    <meta content property="og:title">
    <meta content name="description">
    <meta charset="utf-8">
    <meta content="width=device-width" name="viewport">
</head>

<body style="background-color: #fff;color: #000;font-family: Arial, Helvetica Neue, Helvetica, sans-serif">
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
                    Informe de gastos desde {{ $fecha_inicio }} hasta {{ $fecha_final }} del ciclo.


                </span>
            </h2>
        </div>
    </div>
    <div>
        <h3 style="font-weight: bold;text-align: left;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
            INFORMACION ASOCIADA CICLO</h3>
        <table style="width: 100%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif; ">
            <tr style="text-align: left;">
                <td><b>Presupuesto del ciclo:</b> ${{ number_format(intval($presupuestoCiclo), 0, ',', '.') }}</td>

                <td><b>Total de gastos:</b> ${{ number_format(intval($totalGastado), 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
    <div>
        <div>
            <table style="width: 100%;border: transparent;">
                <caption>
                    <h2
                        style="font-weight: bold;text-align: center;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                        GASTOS</h2>
                </caption>
                @if (count($gastos) == 0)
                    <h4>NO EXISTEN GASTOS ASOCIADOS EN ESAS FECHAS</h4>
                @else
                    <tbody style="text-align: center">
                        @for ($i = 0; $i < count($gastos); $i++)
                            @if ($gastos[$i]['actividad'] == null && $gastos[$i]['competencia'] == null)
                                <tr style="background-color:white;">
                                    <th style="border: transparent;text-align: left;font-weight: normal"><b>FECHA:</b>
                                        {{ date('d/m/Y', strtotime($gastos[$i]['fecha'])) }} - <b>ASOCIADO CON :</b>
                                        NO ASOCIADO</th>
                                </tr>
                            @elseif ($gastos[$i]['actividad'] != null && $gastos[$i]['competencia'] != null)
                                <tr style="background-color:white;">
                                    <th style="border: transparent;text-align: left;font-weight: normal"><b>FECHA:</b>
                                        {{ date('d/m/Y', strtotime($gastos[$i]['fecha'])) }} - <b>ASOCIADO CON :</b>
                                        La actividad: {{ $gastos[$i]['actividad']['nombre'] }} y competencia:
                                        {{ $gastos[$i]['competencia']['tipo'] }}
                                    </th>
                                </tr>
                            @elseif ($gastos[$i]['actividad'] == null && $gastos[$i]['competencia'] != null)
                                <tr style="background-color:white;">
                                    <th style="border: transparent;text-align: left;font-weight: normal"><b>FECHA:</b>
                                        {{ date('d/m/Y', strtotime($gastos[$i]['fecha'])) }} - <b>ASOCIADO CON :</b>
                                        La competencia: {{ $gastos[$i]['competencia']['tipo'] }}</th>
                                </tr>
                            @elseif($gastos[$i]['actividad'] != null && $gastos[$i]['competencia'] == null)
                                <tr style="background-color:white;">
                                    <th style="border: transparent;text-align: left;font-weight: normal"><b>FECHA:</b>
                                        {{ date('d/m/Y', strtotime($gastos[$i]['fecha'])) }} - <b>ASOCIADO CON :</b>
                                        La actividad: {{ $gastos[$i]['actividad']['nombre'] }}</th>
                                </tr>
                            @endif

                            <table
                                style="width: 100%;border: 1px solid #999;border-collapse: collapse;text-align: center;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif; ">
                                <thead>
                                    <tr style="text-align: center;background-color: #f6f6f6;">
                                        <th style="border: 1px solid #999;">NOMBRE DEL DETALLE</th>
                                        <th style="border: 1px solid #999;">VALOR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for ($j = 0; $j < count($gastos[$i]['detalles']); $j++)
                                        <tr>
                                            <td style="width: 50%;border: 1px solid #999;text-align: center;">
                                                {{ $gastos[$i]['detalles'][$j]['nombre'] }}</td>
                                            <td style="width: 50%;border: 1px solid #999;text-align: center;">
                                                ${{ number_format(intval($gastos[$i]['detalles'][$j]['valor']), 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                                <td style="font-weight: bold;text-align: center;">TOTAL:</td>
                                <td style="border: 1px solid #999;">
                                    ${{ number_format(intval($gastos[$i]['valor']), 0, ',', '.') }}
                                </td>
                            </table>
                            <br>
                        @endfor
                    </tbody>
                @endif
            </table>
        </div>
    </div>
    <h6 style="float: right">Fecha de emisión: {{ $fechaEmision }}</h6>
</body>

</html>
