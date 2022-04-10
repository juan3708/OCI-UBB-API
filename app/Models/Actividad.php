<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;
    protected $table = 'actividad';
    public $timestamps = false;

    //Relacion N    

    public function gastos(){
        return $this -> hasMany(Gastos::class);
    }

    //Relacion 1

    public function ciclo(){
        return $this -> belongsTo(Ciclo::class);
    }
}
