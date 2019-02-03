<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
   protected $fillable = [
      'name',
      'descr'
   ];
    //

   public function inationsusers(){
      //associar ligação many to many para tabela inationsusers
      return $this->belongsToMany('App\Inationsuser')->orderBy('inationsusers.name');
   }
}
