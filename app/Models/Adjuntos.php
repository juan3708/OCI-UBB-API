<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Error\Notice;

class Adjuntos extends Model
{
    use HasFactory;
    protected $table = 'adjuntos';
    public $timestamps = false;

    public function noticia(){
        return $this-> belongsTo(Noticia::class);
    }
}
