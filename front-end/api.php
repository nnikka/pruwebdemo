<?php
session_start();
include 'connection.php';


if(isset($_POST['function_name']) && $_POST['function_name'] == "register_company"){
  echo register_company($_POST,$conn);
}
if(isset($_POST['function_name']) && $_POST['function_name'] == "login_company"){
  echo login_company($_POST,$conn);
}

function register_company($data, $conn){
  $sql = "INSERT INTO company (name, email, phone, description, password, pub_key)
      VALUES ('" . $data['name']."','".$data['email']."','".$data['phone']."','".$data['description']."','".$data['password']."','".$data['pub_key']."')";

  if ($conn->query($sql) === TRUE) {
      return 200;
  } else {
     return  $conn->error;
  }
}


function login_company($data, $conn){
  $sql = "SELECT * FROM company where email='".$data['email']."' AND password='".$data['password']."'";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    $_SESSION['company'] = $data['email'];
    echo 200;
  }else{
    echo $conn->error;
  }
}
