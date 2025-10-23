<?php
// require_login.php 
declare(strict_types=1);
session_start();
if (empty($_SESSION['uid'])) {
  header('Location: login.php');
  exit;
}

