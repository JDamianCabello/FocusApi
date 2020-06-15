<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'idUser','event_name', 'event_resume', 'event_date','event_iconId', 'event_color','appnotification','event_notes','idSubject'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
	'created_at', 'updated_at','idUser'

    ];
}
