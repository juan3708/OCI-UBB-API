<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalles extends Model
{
    use HasFactory;
    protected $table = 'detalles';
    public $timestamps = false;

    //Relacion 1

    public function gastos(){
        return $this -> belongsTo(Gastos::class);
    }
}
