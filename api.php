<?php

include 'connection.php';


if(isset($_POST['function_name']) && $_POST['function_name'] == "register_company"){
  echo register_company($_POST,$conn);
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

