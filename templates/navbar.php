<?php
$stmt = $con->prepare("SELECT `id`, `username`, `fullname`, `email`, `password`, `avatar`, `city`, `phone`, `bio` FROM `users`");
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (isset($_SESSION['username'])) {
  if (!empty($user['username']) && !empty($user['fullname']) && !empty($user['email']) && !empty($user['password']) && !empty($user['avatar']) && !empty($user['city']) && !empty($user['phone']) && !empty($user['bio'])) {
  } else {
    echo '<div class="alert alert-warning m-0 text-center">Please fill in the personal information</div>';
  }
}
?>
<nav class="navbar navbar-expand-lg bg-body-tertiary text-capitalize">
  <div class="container">
    <a class="navbar-brand" href="./">JournoWeb</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#my-navs"
      aria-controls="my-navs" aria-expanded="false" aria-label="Toggle navigation">
      <i class="fa-solid fa-bars"></i>
    </button>
    <div class="collapse navbar-collapse" id="my-navs">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <?php
        if (isset($_SESSION['username'])) {
          ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <?php echo $_SESSION['username']; ?>
            </a>
            <ul class="dropdown-menu  dropdown-menu-end">
              <li>
                <a class="dropdown-item" href="profile.php"><i class="fa-solid fa-circle-user"></i>&nbsp;Profile</a>
              </li>
              <li>
                <a class="dropdown-item" href="index.php?do=create-post">
                  <i class="fa-solid fa-circle-plus"></i>&nbsp;Create
                </a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <a class="dropdown-item" href="logout.php">
                  <i class="fa-solid fa-right-from-bracket"></i>&nbsp;Log Out
                </a>
              </li>
            </ul>
          </li>
          <?php
        } else {
          ?>
          <li class="nav-item">
            <a class="nav-link" href="about.php">About</a></a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false"
              aria-label="Toggle Dropdown Menu">
              <i class="fa-solid fa-circle-user"></i>
            </a>
            <ul class="dropdown-menu  dropdown-menu-end">
              <li>
                <a class="dropdown-item" href="index.php?do=enter">
                  <i class="fa-solid fa-right-to-bracket"></i>&nbsp;Enter
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="index.php?do=new-registration">
                  <i class="fa-solid fa-user-plus"></i>&nbsp;Registration
                </a>
              </li>
            </ul>
          </li>
          <?php
        }
        ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container">