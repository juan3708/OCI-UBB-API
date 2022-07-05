<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clase extends Model
{
    use HasFactory;
    protected $table = 'clase';
    public $timestamps = false;

    //Relacion 1
    
    public function ciclo(){
        return $this->belongsTo(Ciclo::class);
    }
        
    public function nivel(){
        return $this->belongsTo(Nivel::class);
    }

    //Relacion N a N

    public function alumnos(){
        return $this->belongsToMany(Alumno::class)->withPivot('asistencia');
    }
    public function ayudantes(){
        return $this->belongsToMany(Ayudante::class);
    }
    public function profesores(){
        return $this->belongsToMany(Profesor::class);
    }
}
