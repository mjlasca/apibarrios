<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cliente extends Model
{
    protected $fillable = [
        'reg',
        'id',
        'nombres',
        'apellidos',
        'tipo_id',
        'fecha_nacimiento',
        'codempresa',
        'telefono',
        'direccion',
        'email',
        'codpostal',
        'sexo',
        'codestado',
        'ultmod'];
    use HasFactory;

    public function propuestas(){
        return $this->hasMany(Propuesta::class);
    }

    protected $casts = [
    'id' => 'string',
    ];
}
