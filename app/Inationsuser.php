<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inationsuser extends Model
{
   protected $fillable = [
      'name',
      'email',
      'obs'
   ];

    //

   public function groups(){
      //associar ligação many to many para tabela groups
      return $this->belongsToMany('App\Group')->orderBy('groups.name');
   }

}

