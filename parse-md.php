<?php
require 'Parsedown.php';
$file = isset($_GET['file']) ? $_GET['file'] : '';
$filePath = './posts/' . $file;
if (!file_exists($filePath)) {
  header('Location: ../404.php');
  exit();
}
$markdownContent = file_get_contents($filePath);
$parsedown = new Parsedown();
$htmlContent = $parsedown->text($markdownContent);
$title = pathinfo($file, PATHINFO_FILENAME);
$title = str_replace('-', ' ', $title);
$title = ucwords($title);
$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
echo "<title>$title</title>";
header('Content-Type: text/html; charset=UTF-8');
echo $htmlContent;
?>