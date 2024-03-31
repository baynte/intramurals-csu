<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participant extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = ['year', 'name', 'description', 'avatar_path', 'bg_path'];

    protected $appends = ['avatar_path'];

    public function getAvatarPathAttribute(){
        return $this->attributes['avatar_path'] ?
            '/storage/'.$this->attributes['avatar_path']
            : 'https://ui-avatars.com/api/?background=84E296&name='.$this->name;
    }
    
    public function getBgPathAttribute(){
        return $this->attributes['bg_path'] ?
        '/storage/'.$this->attributes['bg_path']
        : 'https://ui-avatars.com/api/?background=84E296&name='.$this->name;
    }
}
