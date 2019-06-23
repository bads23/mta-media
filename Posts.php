<?php

require_once('db.php');


class Posts extends db{

  public $table_name;
  
  public function __construct(){
    $this->table_name = 'post_images';
  }
}