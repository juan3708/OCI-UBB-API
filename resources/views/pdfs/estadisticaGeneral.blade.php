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
                    {{ $ciclo['nombre'] }}
                    <br>
                    Informe estadistico del Ciclo.
                </span>
            </h2>
        </div>
    </div>
    <div>
        <h3 style="font-weight: bold;text-align: left;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
            INFORMACION ASOCIADA CICLO</h3>
        <table style="width: 100%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif; ">
            <tr style="text-align: left;">
                <td><b>Nombre:</b> {{ $ciclo['nombre'] }}</td>

                <td><b>Año:</b> {{ $ciclo['anio'] }}</td>
            </tr>
            <br>
            <tr style="text-align: left;">
                <td><b>Fecha de inicio:</b> {{ date('d/m/Y', strtotime($ciclo['fecha_inicio'])) }}</td>

                <td><b>Fecha de termino:</b> {{ date('d/m/Y', strtotime($ciclo['fecha_termino'])) }}</td>
            </tr>
            <br>
            <tr style="text-align: left;">
                <td><b>Coordinador:</b> {{ $ciclo['coordinador']['nombre'] }}
                    {{ $ciclo['coordinador']['apellidos'] }}
                </td>
                @if (count($competencias) >= 1)
                    <td><b>Competencias totales:</b> {{ count($competencias) }}</td>
                @else
                    <td><b>Competencias totales:</b> NO EXISTEN COMPETENCIAS ASOCIADAS</td>
                @endif
            </tr>
            <br>
            <tr style="text-align: left;">
                <td><b>Establecimientos participantes:</b> {{ $cantEstablecimientos }}</td>
                @if ($cicloAnterior != -1)
                    <td><b>Diferencia en comparacion con la OCI anterior ({{ $cicloAnterior['nombre'] }}): </b>
                        {{ $diferenciaEstablecimientos }}</td>
                @else
                    <td><b>Diferencia en comparacion con la OCI anterior: </b>
                        NO EXISTE CICLO ANTERIOR</td>
                @endif
            </tr>
            <br>
            <tr style="text-align: left;">
                <td><b>Cantidad de estudiantes inscritos:</b> {{ $cantidadAlumnosInscritos }}</td>
                @if ($cicloAnterior != -1)
                    <td><b>Diferencia en comparacion con la OCI anterior ({{ $cicloAnterior['nombre'] }}): </b>
                        {{ $diferenciaAlumnosInscritos }}</td>
                @else
                    <td><b>Diferencia en comparacion con la OCI anterior: </b>
                        NO EXISTE CICLO ANTERIOR</td>
                @endif
            </tr>
            <br>
            <tr style="text-align: left;">
                <td><b>Cantidad de estudiantes participantes:</b> {{ $cantidadAlumnosParticipantes }}</td>
                @if ($cicloAnterior != -1)
                    <td><b>Diferencia en comparacion con la OCI anterior ({{ $cicloAnterior['nombre'] }}): </b>
                        {{ $diferenciaAlumnosParticipantes }}</td>
                @else
                    <td><b>Diferencia en comparacion con la OCI anterior: </b>
                        NO EXISTE CICLO ANTERIOR</td>
                @endif
            </tr>
            <br>
            <tr style="text-align: left;">
                <td><b>Establecimiento con mas estudiantes inscritos:</b> {{ $establecimientoMaxInscritos['nombre'] }}
                    con la cantidad de {{ $establecimientoMaxInscritos['Alumnos'] }} alumnos </td>
                <td><b>Establecimiento con menos estudiantes inscritos:</b>
                    {{ $establecimientoMinInscritos['nombre'] }}
                    con la cantidad de {{ $establecimientoMinInscritos['Alumnos'] }} alumnos </td>
            </tr>
            <br>
            <tr style="text-align: left;">
                <td><b>Establecimiento con mas estudiantes participantes:</b>
                    {{ $establecimientoMaxParticipantes['nombre'] }} con la cantidad de
                    {{ $establecimientoMaxParticipantes['Alumnos'] }} alumnos </td>
                <td><b>Establecimiento con menos estudiantes participantes:</b>
                    {{ $establecimientoMinParticipantes['nombre'] }} con la cantidad de
                    {{ $establecimientoMinParticipantes['Alumnos'] }} alumnos </td>
            </tr>
        </table>
    </div>
    <div style="page-break-after:always;"></div>
    <div>
        <div>
            @if (count($establecimientos) == 0)
                <h4 style="font-weight: bold;text-align: center">NO EXISTEN ASISTENCIAS ASOCIADAS.</h4>
            @else
                <table style="width: 100%;border: transparent;">
                    <caption>
                        <h2
                            style="font-weight: bold;text-align: center;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                            ASISTENCIA POR ESTABLECIMIENTOS</h2>
                    </caption>
                    <tbody style="text-align: center">
                        @for ($i = 0; $i < count($establecimientos); $i++)
                            <caption>
                                <h3
                                    style="font-weight: bold;text-align: left;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                                    INFORMACION DEL ESTABLECIMIENTO</h3>
                            </caption>
                            <tr style="background-color:white;">
                                <th style="border: transparent;text-align: left;font-weight: normal"><b>NOMBRE DEL
                                        ESTABLECIMIENTO:</b>
                                    {{ $establecimientos[$i]['nombre'] }} - <b>TOTAL ESTUDIANTES:</b>
                                    {{ count($establecimientos[$i]['alumnos']) }}</th>
                            </tr>
                            @if (count($establecimientos[$i]['alumnos']) == 0 && $i == 0)
                                <h4 style="font-weight: bold;text-align: center">NO EXISTEN ASISTENCIAS ASOCIADAS AL
                                    ESTABLECIMIENTO</h4>
                            @elseif ($i < count($establecimientos) - 1 && count($establecimientos[$i]['alumnos']) == 0)
                                @if (count($establecimientos[$i + 1]['alumnos']) == 0)
                                    <h4 style="font-weight: bold;text-align: center">NO EXISTEN ASISTENCIAS ASOCIADAS AL
                                        ESTABLECIMIENTO</h4>
                                @elseif (count($establecimientos[$i + 1]['alumnos']) > 0)
                                    <h4 style="font-weight: bold;text-align: center">NO EXISTEN ASISTENCIAS ASOCIADAS AL
                                        ESTABLECIMIENTO</h4>
                                    <div style="page-break-after:always;"></div>
                                @endif
                            @elseif ($i == count($establecimientos) - 1)
                                @if (count($establecimientos[$i]['alumnos']) == 0)
                                    <h4 style="font-weight: bold;text-align: center">NO EXISTEN ASISTENCIAS ASOCIADAS AL
                                        ESTABLECIMIENTO</h4>
                                @else
                                    <table
                                        style="width: 100%;border: 1px solid #999;border-collapse: collapse;text-align: center;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif; ">
                                        <caption>
                                            <h3
                                                style="font-weight: bold;text-align: left;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
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
                                            @for ($j = 0; $j < count($establecimientos[$i]['alumnos']); $j++)
                                                <tr>
                                                    <td style="border: 1px solid #999;text-align: center;width: 15%;">
                                                        {{ $establecimientos[$i]['alumnos'][$j]['rut'] }}</td>
                                                    <td style="border: 1px solid #999;text-align: center;width: 14%;">
                                                        {{ $establecimientos[$i]['alumnos'][$j]['nombre'] }}</td>
                                                    <td style="border: 1px solid #999;text-align: center;width: 14%;">
                                                        {{ $establecimientos[$i]['alumnos'][$j]['apellidos'] }}</td>
                                                    <td style="border: 1px solid #999;text-align: center;width: 14%;">
                                                        {{ $establecimientos[$i]['alumnos'][$j]['PorcentajeAsistencia'] }}%
                                                    </td>
                                                    <td style="border: 1px solid #999;text-align: center;width: 14%;">
                                                        {{ $establecimientos[$i]['alumnos'][$j]['CantAsistenciasEInasistencias'][0]['asistencias'] }}
                                                    </td>
                                                    <td style="border: 1px solid #999;text-align: center;width: 14%;">
                                                        {{ $establecimientos[$i]['alumnos'][$j]['CantAsistenciasEInasistencias'][0]['inasistencias'] }}
                                                    </td>
                                                    <td style="border: 1px solid #999;text-align: center;width: 14%;">
                                                        {{ count($establecimientos[$i]['alumnos'][$j]['Asistencias']) }}
                                                    </td>

                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                @endif
                            @else
                                <table
                                    style="width: 100%;border: 1px solid #999;border-collapse: collapse;text-align: center;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif; ">
                                    <caption>
                                        <h3
                                            style="font-weight: bold;text-align: left;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
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
                                        @for ($j = 0; $j < count($establecimientos[$i]['alumnos']); $j++)
                                            <tr>
                                                <td style="border: 1px solid #999;text-align: center;width: 15%;">
                                                    {{ $establecimientos[$i]['alumnos'][$j]['rut'] }}</td>
                                                <td style="border: 1px solid #999;text-align: center;width: 14%;">
                                                    {{ $establecimientos[$i]['alumnos'][$j]['nombre'] }}</td>
                                                <td style="border: 1px solid #999;text-align: center;width: 14%;">
                                                    {{ $establecimientos[$i]['alumnos'][$j]['apellidos'] }}</td>
                                                <td style="border: 1px solid #999;text-align: center;width: 14%;">
                                                    {{ $establecimientos[$i]['alumnos'][$j]['PorcentajeAsistencia'] }}%
                                                </td>
                                                <td style="border: 1px solid #999;text-align: center;width: 14%;">
                                                    {{ $establecimientos[$i]['alumnos'][$j]['CantAsistenciasEInasistencias'][0]['asistencias'] }}
                                                </td>
                                                <td style="border: 1px solid #999;text-align: center;width: 14%;">
                                                    {{ $establecimientos[$i]['alumnos'][$j]['CantAsistenciasEInasistencias'][0]['inasistencias'] }}
                                                </td>
                                                <td style="border: 1px solid #999;text-align: center;width: 14%;">
                                                    {{ count($establecimientos[$i]['alumnos'][$j]['Asistencias']) }}
                                                </td>

                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                                <div style="page-break-after:always;"></div>
                            @endif
                        @endfor
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    <div style="page-break-after:always;"></div>
    <div>
        <div>
            <table style="width: 100%;border: transparent;">
                <caption>
                    <h2
                        style="font-weight: bold;text-align: center;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                        COMPETENCIAS</h2>
                </caption>
                @if (count($competencias) == 0)
                    <h4 style="font-weight: bold;text-align: center">NO EXISTEN COMPETENCIAS ASOCIADAS.</h4>
                @else
                    <tbody>
                        @for ($i = 0; $i < count($competencias); $i++)
                            <tr style="background-color:white;">
                                <th style="border: transparent;text-align: center;font-weight: normal;">
                                    <b>FECHA: </b> {{ date('d/m/Y', strtotime($competencias[$i]['fecha'])) }} -
                                    <b>TIPO DE COMPETICION: </b>{{ $competencias[$i]['tipo'] }} -
                                    <b>LUGAR: </b>{{ $competencias[$i]['lugar'] }}
                                </th>
                            </tr>
                            @if (count($competencias[$i]['alumnos']) == 0)
                                <h4 style="font-weight: bold;text-align: center"> NO EXISTEN ESTUDIANTES ASOCIADOS</h4>
                                <br>
                            @else
                                <br>
                                <tr>
                                    <th style="border: transparent;text-align: left;font-weight: normal;width: 50%">
                                        <b>PUNTAJE PROMEDIO: </b> {{ $competencias[$i]['promedioPuntaje'] }} -
                                        <b>Cantidad total de estudiantes: </b>{{ count($competencias[$i]['alumnos']) }}
                                    </th>
                                </tr>
                                <table
                                    style="width: 100%;border: 1px solid #999;border-collapse: collapse;text-align: center;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif; ">
                                    <thead>
                                        <tr style="text-align: center;background-color: #f6f6f6;">
                                            <th style="border: 1px solid #999;">RUT</th>
                                            <th style="border: 1px solid #999;">NOMBRE</th>
                                            <th style="border: 1px solid #999;">APELLIDOS</th>
                                            <th style="border: 1px solid #999;">ESTABLECIMIENTO</th>
                                            <th style="border: 1px solid #999;">PUNTAJE</th>
                                    </thead>
                                    <tbody>
                                        @for ($j = 0; $j < count($competencias[$i]['alumnos']); $j++)
                                            <tr>
                                                <td style="border: 1px solid #999;text-align: center;">
                                                    {{ $competencias[$i]['alumnos'][$j]['rut'] }}</td>
                                                <td style="border: 1px solid #999;text-align: center;">
                                                    {{ $competencias[$i]['alumnos'][$j]['nombre'] }}</td>
                                                <td style="border: 1px solid #999;text-align: center;">
                                                    {{ $competencias[$i]['alumnos'][$j]['apellidos'] }}</td>
                                                <td style="border: 1px solid #999;text-align: center;">
                                                    {{ $competencias[$i]['alumnos'][$j]['establecimiento']['nombre'] }}
                                                </td>
                                                <td style="border: 1px solid #999;text-align: center;">
                                                    {{ $competencias[$i]['alumnos'][$j]['pivot']['puntaje'] }}
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                                <br>
                                <br>
                            @endif
                        @endfor
                    </tbody>
                @endif
            </table>
        </div>
    </div>
    <div style="page-break-after:always;"></div>

    <table style="border: transparent">
        <caption>
            <h2
                style="font-weight: bold;text-align: center;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                GASTOS</h2>
        </caption>
        @if (count($gastos) == 0)
            <h4
                style="font-weight: bold;text-align: center;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                NO EXISTEN GASTOS ASOCIADOS</h4>
        @else
            <tr style="background-color:white;">
                <th class="presupuestoActual" style="border: transparent;text-align: left;font-weight: normal">
                    <b>PRESUPUESTO
                        ACTUAL:</b>
                    ${{ number_format(intval($ciclo['presupuesto']), 0, ',', '.') }} - <b>PRESUPUESTO
                        RESTANTE:</b> ${{ number_format(intval($prespuestoRestante), 0, ',', '.') }}
                </th>
            </tr>
            <br>
            <tr style="background-color:white;">
                <th style="border: transparent;text-align: left;font-weight: normal;"><b>TOTAL GASTOS:</b>
                    ${{ number_format(intval($totalGastos), 0, ',', '.') }}</th>

            </tr>
            <br>
            <tbody style="text-align: center">
                @for ($i = 0; $i < count($gastos); $i++)
                    @if ($gastos[$i]['actividad'] == null && $gastos[$i]['competencia'] == null)
                        <tr style="background-color:white">
                            <th style="border: transparent;text-align: left;font-weight: normal;">
                                <b>FECHA:</b>
                                {{ date('d/m/Y', strtotime($gastos[$i]['fecha'])) }} - <b>ASOCIADO CON :</b>
                                NO ASOCIADO
                            </th>
                        </tr>
                    @elseif ($gastos[$i]['actividad'] != null && $gastos[$i]['competencia'] != null)
                        <tr>
                            <th style="border: transparent;text-align: left;font-weight: normal;">
                                <b>FECHA:</b>
                                {{ date('d/m/Y', strtotime($gastos[$i]['fecha'])) }} - <b>ASOCIADO CON :</b>
                                La actividad: {{ $gastos[$i]['actividad']['nombre'] }} y competencia:
                                {{ $gastos[$i]['competencia']['tipo'] }}
                            </th>
                        </tr>
                    @elseif ($gastos[$i]['actividad'] == null && $gastos[$i]['competencia'] != null)
                        <tr style="background-color:white">
                            <th style="border: transparent;text-align: left;font-weight: normal;">
                                <b>FECHA:</b>
                                {{ date('d/m/Y', strtotime($gastos[$i]['fecha'])) }} - <b>ASOCIADO CON :</b>
                                La competencia: {{ $gastos[$i]['competencia']['tipo'] }}
                            </th>
                        </tr>
                    @elseif($gastos[$i]['actividad'] != null && $gastos[$i]['competencia'] == null)
                        <tr style="background-color:white">
                            <th style="border: transparent;text-align: left;font-weight: normal">
                                <b>FECHA:</b>
                                {{ date('d/m/Y', strtotime($gastos[$i]['fecha'])) }} - <b>ASOCIADO CON :</b>
                                La actividad: {{ $gastos[$i]['actividad']['nombre'] }}
                            </th>
                        </tr>
                    @endif
                    <table
                        style="width: 100%;border: 1px solid #999;border-collapse: collapse;text-align: center;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif; ">
                        <thead>
                            <tr style="text-align: center;background-color: #f6f6f6">
                                <th style="border: 1px solid #999;width: 50%">NOMBRE DEL DETALLE</th>
                                <th style="border: 1px solid #999;width: 50%">VALOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($j = 0; $j < count($gastos[$i]['detalles']); $j++)
                                <tr>
                                    <td style="width:100% ;border: 1px solid #999;text-align: center;">
                                        {{ $gastos[$i]['detalles'][$j]['nombre'] }}</td>
                                    <td style="width:100% ;border: 1px solid #999;text-align: center;">
                                        ${{ number_format(intval($gastos[$i]['detalles'][$j]['valor']), 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                        <td style="width:100% ;font-weight: bold;text-align: center;">TOTAL:</td>
                        <td style="width:100% ;border: 1px solid #999;">
                            ${{ number_format(intval($gastos[$i]['valor']), 0, ',', '.') }}
                        </td>
                    </table>
                    <br>
                @endfor
            </tbody>
        @endif
    </table>
    <h6 style="float: right">Fecha de emisión: {{$fechaEmision}}</h6>

</body>
</html>
