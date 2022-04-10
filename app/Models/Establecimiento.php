<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Establecimiento extends Model
{
    use HasFactory;
    protected $table = 'establecimiento';
    public $timestamps = false;

    //Relacion N

    public function alumnos(){
        return $this->hasMany(Alumno::class);
    }

    //Relacion N a N

    public function ciclos(){
        return $this->belongsToMany(Ciclo::class)->withPivot('cupos');
    }
}
