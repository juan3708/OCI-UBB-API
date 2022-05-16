<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;
    protected $table = 'alumno';
    public $timestamps = false;
    protected $fillable = [
        'rut',
        'nombre',
        'apellidos',
        'telefono',
        'email',
        'fecha_nacimiento',
        'curso',
        'direccion',
        'telefono_apoderado',
        'nombre_apoderado',
        'establecimiento_id'];

    //Relacion N a N

    public function clases()
    {
        return $this->belongsToMany(Clase::class)->withPivot('asistencia');
    }
    public function competencias()
    {
        return $this->belongsToMany(Competencia::class)->withPivot('puntaje');
    }

    public function niveles()
    {
        return $this->belongsToMany(Nivel::class);
    }

    public function ciclos()
    {
        return $this->belongsToMany(Ciclo::class)->withPivot('inscrito', 'participante');
    }

    //Relacion 1

    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
    }
}
