<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departament extends Model
{
    use HasFactory;

    protected $guarded = [];//Desactiva todo y no es recomendable en produccion solo para esta practica y que sea rapido de realizar
}
