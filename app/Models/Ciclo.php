<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\FuncCall;

class Ciclo extends Model
{
    use HasFactory;
    protected $table = 'ciclo';
    public $timestamps = false;


    //Relaciones N
    public function actividades()
    {
        return $this -> hasMany(Actividad::class);
    }
    public function noticias()
    {
        return $this -> hasMany(Noticia::class);
    }
    public function gastos(){
        return $this -> hasMany(Gastos::class);
    }
    public function competencias(){
        return $this -> hasMany(Competencia::class);
    }
    public function clases(){
        return $this->hasMany(Clase::class);
    }

    //Relaciones 1

    public function coordinador(){
        return $this -> belongsTo(Coordinador::class);
    }

    //Relaciones N a N

    public function establecimientos(){
        return $this->belongsToMany(Establecimiento::class)->withPivot('cupos');
    }

}
