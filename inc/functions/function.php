<?php
function show_message($message, $type = 'success')
{
  if ($type == 'success') {
    $_SESSION['message'] = '<div class="alert alert-success">' . $message . '</div>';
  } else {
    $_SESSION['message'] = '<div class="alert alert-danger">' . $message . '</div>';
  }
}
function sanitizeTitle($title)
{
  $title = str_replace(' ', '-', $title);
  $title = preg_replace('/[^A-Za-z0-9\-]/', '', $title);
  $title = strtolower($title);

  return $title;
}
function checkImageDimensions($file_tmp)
{
  list($width, $height) = getimagesize($file_tmp);
  return $width === $height;
}