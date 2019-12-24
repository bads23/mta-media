<?php

Class Images {

  public function __construct(){
    
  }

  public function uploader($data, $files){
    $is_valid = $this->validate($data, $files);

    if($is_valid === true){
      $name = $this->mkFilename($data);
      
      if(!file_exists('media/'.$data['category'].'/')){
        mkdir('media/'.$data['category'].'/');
      }

      $ext = strtolower(pathinfo($files['image']['name'], PATHINFO_EXTENSION));
      $path = 'media/'.$data['category'].'/'.$name.'.'.$ext;
      
      $msg = $files['image']['tmp_name'];
      $upload = move_uploaded_file($msg,$path);
      
      if($upload){
        $response = $this->save_to_django($path, $data);
        return $response;
      } else {
        return false;
      }

    } else {
      return $is_valid;
    }
  }

  public function save_to_django($path, $data){

    if($data['category'] === 'products'){

      $post = [
        'catalog' => $data['catalogue_id'],
        'path' => $path,
        'is_avatar' => False
      ];

      $ch = curl_init('https://b23.pythonanywhere.com/images/');
      // $ch = curl_init('http://localhost:8000/images/');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

      try{
        $response = curl_exec($ch);
        return true;
      } catch(exception $e){
        return false;
      }


    } else if($data['category'] === 'posts'){

      $post = [
        'posts' => $data['post_id'],
        'Cover_Image' => $path
      ];

      $ch = curl_init('https://b23.pythonanywhere.com/posts/news/'. $data['post_id'] .'/');
      // $ch = curl_init('http://localhost:8000/posts/news/'. $data['post_id'] .'/');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

      try{
        $response = curl_exec($ch);
        return true;
      } catch(exception $e){
        return false;
      }

    } else if ($data['category'] === 'clients'){
      
      $post = [
        'profile_photo' => $path
      ];

      $ch = curl_init('https://b23.pythonanywhere.com/clients/clients/'. $data['client_id'] .'/');
      // $ch = curl_init('http://localhost:8000/clients/clients/'. $data['client_id'] .'/');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

      try{
        $response = curl_exec($ch);
        return true;
      } catch(exception $e){
        return false;
      }
    } 

  }

  public function validate($data, $files){
    //check if files are present.
    $exists = (count($files) > 0) ? true : false ;

    //check if file is an image file
    $is_image = getimagesize($files['image']['tmp_name']) ? true : false ;

    //sanitize data
    // $is_int = (gettype((int)$data['catalogue_id']) == 'integer') ? true : false ;
    
    if($exists && $is_image){
      return true;
    } else if(!$exists){
      return false;
    } else if(!$is_image){
      return false;
    }

  }

  public function mkFilename($data){
    $salt = sha1(rand());
    $salt = substr($salt, 0, 25);
    $cat = str_split($data['category'], 1);
    return $cat[0].$cat[1].$salt;
  }

}