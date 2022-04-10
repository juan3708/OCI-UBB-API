<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coordinador extends Model
{
    use HasFactory;
    protected $table = 'coordinador';
    public $timestamps = false;

    //Relacion N

    public function ciclos(){
        return $this->hasMany(Ciclo::class);
    }
}
