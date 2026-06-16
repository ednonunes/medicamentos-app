<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationLog extends Model
{
    use HasFactory;

    protected $fillable = ['medication_id', 'scheduled_time', 'taken_at'];

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }
}