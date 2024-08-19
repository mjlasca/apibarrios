<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cola extends Model
{
    use HasFactory;
    protected $fillable = [
        'entity',
        'entity_id',
        'codempresa',
        'ptoventa'
    ];

}
