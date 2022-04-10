<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    use HasFactory;
    protected $table = 'noticia';
    public $timestamps = false;

    //Relacion N

    public function adjuntos(){
        return $this ->hasMany(Adjuntos::class);
    }

    //Relacion 1
    public function user(){
        return $this ->belongsTo(User::class);
    }
}
