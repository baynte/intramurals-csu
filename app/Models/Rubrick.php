<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rubrick extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'title', 'year'
    ];

    public function insights(){
        return $this->hasMany(RubrickInsight::class, 'rubrick_id', 'id')
            ->orderBy('created_at', 'desc');
    }
}
