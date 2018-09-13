<?php 
session_start();
if(!isset($_SESSION['company']) || $_SESSION['company'] == null){
    header("Location:index.php");
}