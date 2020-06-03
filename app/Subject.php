<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
       protected $fillable = [
         'idUser','subject_name','exam_date','color','iconId'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'idUser', 'created_at', 'updated_at'
    ];
}

