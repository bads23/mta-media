<?php
error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_log', 'php-errors.log');


$method = $_SERVER['REQUEST_METHOD'];

spl_autoload_register(function ($class_name) {
  include $class_name . '.php';
});

$images = new Images();
$Getter = new Getter();
$products = new Products();
$posts = new Posts();

if($method === 'POST'){
  $data = $_POST;
  $files = $_FILES;

  $path = $images->uploader($data, $files);

  if(!$path){
    echo 'Unable To upload Image!';
  } else {
    $data['path'] = $path;
    if($data['category'] === 'products'){
      echo $products->insert($data);
    } else if($data['category'] === 'posts'){
      echo $posts->insert($data);
    }
  }

} else if($method === 'GET') {
  $q_str = $_SERVER['QUERY_STRING'];
  echo $Getter->get($q_str);
}