<?php

// Start Connect to database
include 'connect.php';

// Start directory includes
$func = 'inc/functions/';
$tpl = 'templates/';
$css = 'assets/css/';
$js = 'assets/js/';
$img = 'assets/images/';

// Start include Files
include $func . 'function.php';
include $tpl . 'header.php';


if (!isset($noNavbar)) {
  include $tpl . "navbar.php";
}
?>