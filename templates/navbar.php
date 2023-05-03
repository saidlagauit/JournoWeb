<nav class="navbar navbar-expand-lg bg-body-tertiary text-capitalize">
  <div class="container">
    <a class="navbar-brand" href="./index.php">
      <?php echo $lang['JournoWeb'] ?>
    </a>
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
                <a class="dropdown-item" href="profile.php"><i class="fa-solid fa-circle-user"></i>&nbsp;
                  <?php echo $lang['Profile'] ?>
                </a>
              </li>
              <li>
              <li>
                <a class="dropdown-item" href="index.php?do=create-post">
                  <i class="fa-solid fa-circle-plus"></i>&nbsp;
                  <?php echo $lang['Create'] ?>
                </a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <a class="dropdown-item" href="profile.php?do=management-posts">
                  <i class="fa-solid fa-screwdriver-wrench"></i>&nbsp;
                  <?php echo $lang['Articles'] ?>
                </a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <a class="dropdown-item" href="logout.php">
                  <i class="fa-solid fa-right-from-bracket"></i>&nbsp;
                  <?php echo $lang['Log Out'] ?>
                </a>
              </li>
            </ul>
          </li>
          <?php
        } else {
          ?>
          <li class="nav-item">
            <a class="nav-link" href="about.php">
              <?php echo $lang['About'] ?>
            </a></a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false"
              aria-label="Toggle Dropdown Menu">
              <i class="fa-solid fa-circle-user"></i>
            </a>
            <ul class="dropdown-menu  dropdown-menu-end">
              <li>
                <a class="dropdown-item" href="index.php?do=enter">
                  <i class="fa-solid fa-right-to-bracket"></i>&nbsp;
                  <?php echo $lang['Enter'] ?>
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="index.php?do=new-registration">
                  <i class="fa-solid fa-user-plus"></i>&nbsp;
                  <?php echo $lang['Registration'] ?>
                </a>
              </li>
            </ul>
          </li>
          <?php
        }
        ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
          <div class="form-group">
            <select name="language" class="form-control" onchange="this.form.submit()">
              <option value="en" <?php if ($selectedLanguage === 'en')
                echo 'selected'; ?>>
                <?php echo $lang['English'] ?>
              </option>
              <option value="ar" <?php if ($selectedLanguage === 'ar')
                echo 'selected'; ?>>
                <?php echo $lang['Arabic'] ?>
              </option>
            </select>
          </div>
        </form>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
