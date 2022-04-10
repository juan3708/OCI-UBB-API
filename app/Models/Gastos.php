<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gastos extends Model
{
    use HasFactory;
    protected $table = 'gastos';
    public $timestamps = false;

    //Relacion N

    public function detalles(){
        return $this -> hasMany(Detalles::class);
    }

    //Relacion 1

    public function actividad(){
        return $this -> belongsTo(Actividad::class);
    }
    public function competencia(){
        return $this ->belongsTo(Competencia::class);
    }
    public function ciclo(){
        return $this->belongsTo(Ciclo::class);
    }
}
