<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['language'])) {
  $selectedLanguage = $_POST['language'];
  $_SESSION['language'] = $selectedLanguage;
  header('Location: ' . $_SERVER['PHP_SELF']);
  exit;
}
if (isset($_SESSION['language'])) {
  $selectedLanguage = $_SESSION['language'];
} else {
  $browserLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
  $supportedLanguages = ['en', 'ar'];
  $selectedLanguage = in_array($browserLanguage, $supportedLanguages) ? $browserLanguage : 'en';

  $_SESSION['language'] = $selectedLanguage;
}

if ($selectedLanguage === 'ar') {
  include $lang . "Arabic.php";
} else {
  include $lang . "English.php";
}
?>
<!DOCTYPE html>
<html dir="<?php echo $lang['LTR']; ?>" lang="<?php echo $lang['EN']; ?>">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>JournoWeb</title>
  <meta name="description"
    content="JournoWeb is the ultimate platform for journalists and writers to publish and share their articles. Create captivating content in Markdown format, engage with readers, and establish your professional reputation. Join the vibrant world of journalism with JournoWeb today!" />
  <link rel="stylesheet" href="<?php echo $css; ?><?php echo $lang['BOOTSTRAP']; ?>" />
  <link rel="stylesheet" href="<?php echo $css; ?>all.min.css" />
  <link rel="stylesheet" href="<?php echo $css; ?>main.css" />
</head>

<body>
