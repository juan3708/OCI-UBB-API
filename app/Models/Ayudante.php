<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ayudante extends Model
{
    use HasFactory;
    protected $table = 'ayudante';
    public $timestamps = false;

    //Relacion N a N

    public function clases(){
        return $this->belongsToMany(Clase::class);
    }
}
