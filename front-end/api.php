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
  $sql = "INSERT INTO company (name, email, phone, description, pub_key, public_key_elliptic)
      VALUES ('" . $data['name']."','".$data['email']."','".$data['phone']."','".$data['description']."','".$data['pub_key']."','".$data['public_key_elliptic']."')";

  if ($conn->query($sql) === TRUE) {
      return 200;
  } else {
     return  $conn->error;
  }
}


function login_company($data, $conn){
  $sql = "SELECT * FROM company where public_key_elliptic='".$data['public_key_elliptic']."'";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {;
    $row = $result->fetch_assoc();
    $_SESSION['company'] = $row["name"];
    echo 200;
  }else{
    echo $conn->error;
  }
}
