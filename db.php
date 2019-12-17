<?php

class db{

  public $db_name = 'motiontalent_media';
  public $host = 'localhost';
  public $db_user = 'motiontalent_karuma';
  public $db_pass = 'K@z33@4546!';
  public $conn;

  public function __construct(){
    // $this->conn = new PDO('mysql:host='.$this->host.';dbname='.$this->db_name, $this->db_user, $this->db_pass);
  }

  public function Create($req){
      
    $sql = 'INSERT INTO '.$req['table_name'].' SET '.$req['params'];
    $query = $this->conn->prepare($sql);
    $query -> execute($req['array']);
    $last_id = $this->conn->lastInsertId();
    if($query == false){
      return false;
    } else {
      return $last_id;
    }
    
  }

  public function Read($req){
      
    $sql = 'SELECT '.$req['cols'].' FROM '.$req['table_name'].' '.$req['params'];
    $query = $this->conn->prepare($sql);
    $query->execute($req['array']);
    
    $data = $query->fetchAll(PDO::FETCH_ASSOC);

    if($data === null){
      return false;
    } else {
      return $data;
   }
   
  }

  public function Update($req){
    $sql = 'UPDATE '. $req['table_name'] .' SET '.$req['params'];
    $query = $this->conn->prepare($sql);
    $query -> execute($req['array']);
    if($query == false){
      return false;
    } else {
      return true;
    }
  }
  

  public function Thanos($req){
    $sql = 'DELETE FROM '. $req['table_name'].' '.$req['params'];
    $query = $this->conn->prepare($sql);
    $query -> execute($req['array']);
    if($query){
      return true;
    } else {
      return false;
    }
  }
  
} 