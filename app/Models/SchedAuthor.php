<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchedAuthor extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'enabled', 'sched_id', 'name', 'position'
    ];

    protected $appends = ['avatar'];

    public function getAvatarAttribute(){
        return "https://ui-avatars.com/api/?name=".$this->name;
    }
}
