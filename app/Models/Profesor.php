<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profesor extends Model
{
    use HasFactory;
    protected $table = 'profesor';
    public $timestamps = false;

    //Relaciones N a N

    public function clases(){
        return $this->belongsToMany(Clase::class);
    }
}
