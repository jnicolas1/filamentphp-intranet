<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    use HasFactory;
    protected $guarded = [];//Desactiva todo y no es recomendable en produccion solo para esta practica y que sea rapido de realizar

    public function user()  {
        return $this->belongsTo(User::class);        
    }
    public function calendar() {
        return $this->belongsTo(Calendar::class);
    }
}
