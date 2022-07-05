<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nivel extends Model
{
    use HasFactory;
    protected $table = 'nivel';
    public $timestamps = false;

    //Relacion 1
    
    public function ciclo(){
        return $this->belongsTo(Ciclo::class);
    }

    //Relacion N

    public function clases(){
        return $this->hasMany(Clase::class);
    }

    //Relacion N a N

    public function alumnos(){
        return $this->belongsToMany(Alumno::class);
    }
}
