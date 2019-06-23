<?php

require_once('db.php');

class Products extends db{
  public $table_name;

  public function __construct(){
    parent::__construct();
    $this->table_name = 'product_images';
  }

  public function Get($obj){

    $r['cols'] = '*';
    $r['table_name'] = $this->table_name;
    if(count($obj) > 1){
      $r['params'] = 'WHERE catalogue_id=:catalogue_id';
    } else {
      $r['params'] = '';
    }

    $r['array'] = [
      ':catalogue_id' => $obj[1]
    ];

    $images = $this->Read($r);

    if($images){
      return json_encode($images);
    } else {
      return 'false';
    }

  }

  public function Insert($obj){
    
    $r['table_name'] = $this->table_name;
    $r['params'] = '
      catalogue_id = :catalogue_id,
      path = :path
    ';
    $r['array'] = [
      ":catalogue_id" => $obj['catalogue_id'],
      ":path" => $obj['path']
    ];

    $insert = $this->Create($r);

    if($insert){
      return $insert;
    } else {
      return 'false';
    }


  }

}