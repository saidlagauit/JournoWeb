<?php
session_start();
include 'init.php';
if (isset($_SESSION['username'])) {

  $do = isset($_GET['do']) ? $_GET['do'] : 'profile';

  if ($do == 'profile') {
    $id = $_SESSION['id'];
    $stmt = $con->prepare("SELECT `username`, `fullname`, `email`, `password`, `avatar`, `status`, `created`, `city`, `phone`, `bio` FROM `users` WHERE `id` = ? LIMIT 1");
    $stmt->execute(array($id));
    $userInfo = $stmt->fetch();
    ?>
    <?php if (isset($_SESSION['message'])): ?>
      <div id="message">
        <?php echo $_SESSION['message']; ?>
      </div>
      <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <div class="row">
      <?php
      if (!empty($userInfo['avatar'])) {
        ?>
        <div class="col-md-4">
          <img class="img-profile rounded-circle" src="./uploads/profiles/<?php echo $userInfo['avatar']; ?>"
            alt="<?php echo $userInfo['fullname']; ?>">
        </div>
        <?php
      } else {
      }
      ?>
      <div class="col-md-6 mx-auto">
        <div class="profile text-capitalize">
          <h1>profile :
            <?php echo $userInfo['fullname']; ?>
            <a class="btn btn-danger" href="profile.php?do=delete-profile"><i class="fa-solid fa-user-slash"></i></a>
          </h1>
          <form method="post" action="profile.php?do=update" autocomplete="off" enctype="multipart/form-data">
            <div class="form-group mb-3">
              <label>Username:&nbsp;<sub class="text-danger">Usernames cannot be changed.</sub></label>
              <input type="hidden" name="id" value="<?php echo $_SESSION['id']; ?>">
              <input name="username" class="form-control" value="<?php echo $userInfo['username']; ?>" required="required"
                style="pointer-events: none;" />
            </div>
            <div class="input-group mb-3">
              <label>Profile Image</label>
              <div id="avatar" class="input-group">
                <input type="hidden" name="avatar_old" value="<?php echo $userInfo['avatar']; ?>">
                <input type="file" accept=".png" name="avatar" />
              </div>
            </div>
            <div class="form-group mb-3">
              <label>Full Name:</label>
              <input class="form-control" name="fullname" value="<?php echo $userInfo['fullname']; ?>">
            </div>
            <div class="form-group mb-3">
              <label>Email:</label>
              <input class="form-control" name="email" value="<?php echo $userInfo['email']; ?>">
            </div>
            <div class="form-group mb-3">
              <label>City:</label>
              <input class="form-control" name="city" value="<?php echo $userInfo['city']; ?>">
            </div>
            <div class="form-group mb-3">
              <label>Phone:</label>
              <input class="form-control" name="phone" value="<?php echo $userInfo['phone']; ?>">
            </div>
            <div class="form-group mb-3">
              <label>Bio:</label>
              <textarea class="form-control" name="bio" rows="4"><?php echo $userInfo['bio']; ?></textarea>
            </div>
            <div class="form-group mb-3">
              <label>New Password</label>
              <input type="hidden" name="password-old" class="form-control" value="<?php echo $userInfo['password']; ?>" />
              <input type="password" name="password-new" class="form-control"
                placeholder="Leave Blank If You Done Want To Change" />
            </div>
            <button type="submit" class="btn btn-primary mb-3">Update Profile</button>
          </form>
        </div>
      </div>
    </div>
    <?php
  } elseif ($do == 'delete-profile') {
    ?>
    <div class="deleted">
      <form class="mb-3" method="post" action="profile.php?do=delete-true">
        <h2>Delete Profile</h2>
        <p>Are you sure you want to delete your profile? This action cannot be undone.</p>
        <input type="hidden" name="id" value="<?php echo $_SESSION['id']; ?>">
        <button type="submit" name="delete" class="btn btn-danger"><i class="fa-solid fa-trash"></i>&nbsp;Delete</button>
      </form>
    </div>
    <?php
  } elseif ($do == 'delete-true') {
    $id = $_POST['id'];
    $stmt = $con->prepare("DELETE FROM users WHERE `users`.`id` = ?");
    $stmt->execute([$id]);
    if ($stmt->rowCount() > 0) {
      show_message('Profile deleted successfully', 'success');
      header('Location: logout.php');
      exit();
    } else {
      show_message('Deletion failed', 'danger');
      header('Location: profile.php');
      exit();
    }
  } elseif ($do == 'update') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $id = $_POST['id'];
      $username = $_POST['username'];
      $fullname = $_POST['fullname'];
      $email = $_POST['email'];
      $city = $_POST['city'];
      $phone = $_POST['phone'];
      $bio = $_POST['bio'];
      $password = empty($_POST['password-new']) ? $_POST['password-old'] : sha1($_POST['password-new']);
      $upload_dir = './uploads/profiles/';
      if (!empty($_FILES['avatar']['name'])) {
        $file_ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $avatar = uniqid() . '.' . $file_ext;
        $file_tmp = $_FILES['avatar']['tmp_name'];
        if (!checkImageDimensions($file_tmp)) {
          show_message('The image dimensions must be equal.', 'danger');
          header('location: profile.php');
          exit();
        }
        move_uploaded_file($file_tmp, $upload_dir . $avatar);
      } else {
        $avatar = $_POST['avatar_old'];
      }
      $stmt = $con->prepare("UPDATE `users` SET `username`= ?,`fullname`= ?,`email`= ?,`password`= ?,`avatar`= ?,`city`= ?,`phone`= ?,`bio`= ? WHERE `id`= ?");
      $stmt->execute([$username, $fullname, $email, $password, $avatar, $city, $phone, $bio, $id]);
      show_message('Profile updated successfully.', 'success');
      header("Location: profile.php");
      exit();
    } else {
      show_message('Invalid request.', 'danger');
      header("Location: profile.php");
      exit();
    }
  }
} else {
  header('location: index.php?do=enter');
  exit();
}
include $tpl . 'footer.php'; ?>