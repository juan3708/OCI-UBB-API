<?php

namespace App\Imports;

use App\Models\Alumno;
use App\Models\Establecimiento;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    private $establecimientos;
    private $ciclo_id;

    public function __construct($ciclo_id)
    {
        $this->establecimientos = Establecimiento::all()->pluck('id', 'nombre');
        $this->ciclo_id = $ciclo_id;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $alumno = Alumno::where('rut',$row['rut'])->get();
        //var_dump($alumno);
        if ($alumno ->isEmpty()) {
            $alumno = new Alumno([
            'rut' => $row['rut'],
            'nombre' => $row['nombre'],
            'apellidos' => $row['apellidos'],
            'telefono' => $row['numero_de_telefono'],
            'email' => $row['correo'],
            'fecha_nacimiento' =>\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_de_nacimiento'])->format('Y-m-d'),
            'curso' => $row['curso'],
            'direccion' => $row['direccion'],
            'telefono_apoderado' => $row['numero_de_telefono_del_apoderado'],
            'nombre_apoderado' => $row['nombre_y_apellidos_del_apoderado'],
            'establecimiento_id' => $this->establecimientos[strtoupper($row['nombre_del_establecimiento'])]

        ]);
            $alumno -> save();
            $alumno = Alumno::find($alumno->id);
            $alumno ->ciclos() ->attach($this->ciclo_id, ['participante' => false]);
        } else {
            //var_dump('PASO EL IF');
            $alumno = Alumno::find($alumno[0]->id);
            $alumno ->ciclos() ->attach($this->ciclo_id, ['participante' => false]);
        }

        return $alumno;
    }

    public function rules(): array
    {
        return [
            'rut' => ['required'],
            'nombre' => ['required'],
            'apellidos' => ['required'],
            'numero_de_telefono' => ['required'],
            'correo' => ['required','email:rfc,dns'],
            'fecha_de_nacimiento' => ['required'],
            'curso' => ['required'],
            'direccion' => ['required'],
            'numero_de_telefono_del_apoderado' => ['required'],
            'nombre_y_apellidos_del_apoderado' => ['required'],
            'nombre_del_establecimiento' => ['required'],
        ];
    }
}
