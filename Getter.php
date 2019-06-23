<?php

spl_autoload_register(function ($class_name) {
  include $class_name . '.php';
});

class Getter{

  public $products;
  public $posts;

  public function __construct(){
    $this->products = new Products();
    $this->posts = new Posts();
  }

  public function Get($q_str){
    $data = explode('/', $q_str);
    
    switch ($data[0]){

      case 'products':
        return $this->products->Get($data);
        break;

      case 'posts':
        return $this->posts->handler($data);
        break;

      default:
        return 0;
        break;
    }


  }
  
}