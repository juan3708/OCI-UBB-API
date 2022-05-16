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
        $alumno = Alumno::all()->firstWhere($row['rut']);
        if (empty($alumno)) {
            var_dump($row);
            var_dump($this->establecimientos[strtoupper($row['nombre_del_establecimiento'])]);
            $alumno = new Alumno([
            'rut' => $row['rut'],
            'nombre' => $row['nombre'],
            'apellidos' => $row['apellidos'],
            'telefono' => $row['numero_de_telefono'],
            'email' => $row['correo'],
            'fecha_nacimiento' =>\Carbon\Carbon::parse($row['fecha_de_nacimiento'])->format('Y-m-d'),
            'curso' => $row['curso'],
            'direccion' => $row['direccion'],
            'telefono_apoderado' => $row['numero_de_telefono_del_apoderado'],
            'nombre_apoderado' => $row['nombre_y_apellidos_del_apoderado'],
            'establecimiento_id' => $this->establecimientos[strtoupper($row['nombre_del_establecimiento'])]

        ]);
            $alumno -> save();
            var_dump($alumno);
            $alumno ->ciclos() ->attach($this->ciclo_id, ['inscrito'=>true, 'participante' => false]);
        } else {
            $alumno ->ciclos() ->attach($this->ciclo_id, ['inscrito'=>true, 'participante' => false]);
        }

        return $alumno;
    }

    public function rules(): array
    {
        return [
            'rut' => ['required','unique:alumno,rut'],
            'nombre' => ['required'],
            'apellidos' => ['required'],
            'numero_de_telefono' => ['required'],
            'correo' => ['required','email:rfc,dns','unique:alumno,email'],
            'fecha_de_nacimiento' => ['required'],
            'curso' => ['required'],
            'direccion' => ['required'],
            'numero_de_telefono_del_apoderado' => ['required'],
            'nombre_y_apellidos_del_apoderado' => ['required'],
            'nombre_del_establecimiento' => ['required'],
        ];
    }
}
