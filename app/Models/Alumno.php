<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;
    protected $table = 'alumno';
    public $timestamps = false;

    //Relacion N a N

    public function clases(){
        return $this->belongsToMany(Clase::class)->withPivot('asistencia');
    }
    public function competencias(){
        return $this->belongsToMany(Competencia::class)->withPivot('puntaje');
    }

    public function niveles(){
        return $this->belongsToMany(Nivel::class);
    }

    //Relacion 1

    public function establecimiento(){
        return $this->belongsTo(Establecimiento::class);
    }
}
