<?php
include_once 'config/Databse.php';
include_once 'class/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

include_once('inc/header.php');
?>