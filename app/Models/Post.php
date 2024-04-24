<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['year', 'name', 'description', 'bg_path'];

    protected $appends = ['bg_path'];
    
    public function getBgPathAttribute(){
        return $this->attributes['bg_path'] ?
        '/storage/'.$this->attributes['bg_path']
        : 'https://ui-avatars.com/api/?background=84E296&name='.$this->name;
    }
}
