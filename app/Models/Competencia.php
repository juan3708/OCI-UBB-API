<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competencia extends Model
{
    use HasFactory;
    protected $table = 'competencia';
    public $timestamps = false;

    //Relacion N

    public function gastos(){
        return $this->hasMany(Gastos::class);
    }

    //Relacion 1

    public function ciclo(){
        return $this->belongsTo(Ciclo::class);
    }

    //Relacion N a N

    public function alumnos(){
        return $this->belongsToMany(Alumno::class)->withPivot('puntaje');
    }
}
