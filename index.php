<?php
session_start();
include 'init.php';

$articlesPerPage = 20;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$AllPosts = $con->prepare("SELECT `id`, `title_p`, `author_p`, `status_p`, `posting_p` FROM `posts` ORDER BY `posting_p` DESC");
$AllPosts->execute();
$totalPosts = $AllPosts->rowCount();
$totalPages = ceil($totalPosts / $articlesPerPage);
$limitStart = ($page - 1) * $articlesPerPage;
$limitEnd = $articlesPerPage;
$AllPosts = $con->prepare("SELECT `id`, `title_p`, `author_p`, `status_p`, `posting_p` FROM `posts` ORDER BY `posting_p` DESC LIMIT ?, ?");
$AllPosts->bindValue(1, $limitStart, PDO::PARAM_INT);
$AllPosts->bindValue(2, $limitEnd, PDO::PARAM_INT);
$AllPosts->execute();

$do = isset($_GET['do']) ? $_GET['do'] : 'view';

if ($do == 'view') {
  ?>
  <div class="show-posts mt-3">
    <?php if (isset($_SESSION['message'])): ?>
      <div id="message">
        <?php echo $_SESSION['message']; ?>
      </div>
      <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <div class="row g-3">
      <div class="col-md-6 mx-auto">
        <ul class="text-capitalize">
          <?php
          while ($post = $AllPosts->fetch(PDO::FETCH_ASSOC)) {
            $postId = $post['id'];
            $postTitle = $post['title_p'];
            $postAuthor = $post['author_p'];
            $dateString = $post['posting_p'];
            $now = date('Y-m-d H:i:s');
            $timeDiff = strtotime($now) - strtotime($dateString);
            if ($timeDiff > 86400) {
              $formattedDate = date('d-M', strtotime($dateString));
            } else {
              $formattedDate = date('H:i', strtotime($dateString));
            }
            ?>
            <li class="nav-item position-relative h3 text-bg-light rounded text-truncate">
              <a class="nav-link " href="./posts/<?php echo sanitizeTitle($postTitle) . '.md'; ?>" target="_blank"><?php echo $postTitle; ?>
                <sub class="text-bg-dark rounded p-1">
                  <?php echo $formattedDate; ?>
                </sub>
              </a>
              <div class="position-absolute d-flex top-50 end-0 translate-middle-y">
                <?php
                if (isset($_SESSION['username'])) {
                  $checkSaved = $con->prepare("SELECT COUNT(*) FROM saved_articles WHERE user_id = ? AND article_id = ?");
                  $checkSaved->execute(array($_SESSION['id'], $postId));
                  $isSaved = ($checkSaved->fetchColumn() > 0);
                  if ($isSaved) {
                    ?>
                    <form method="post" action="index.php?do=save">
                      <input type="hidden" name="id" value="<?php echo $postId; ?>">
                      <button class="btn btn-primary" type="submit" aria-label="Delete Saved">
                        <i class="fa-solid fa-bookmark"></i>
                      </button>
                    </form>
                    <?php
                  } else {
                    ?>
                    <form method="post" action="index.php?do=save">
                      <input type="hidden" name="id" value="<?php echo $postId; ?>">
                      <button class="btn btn-primary" type="submit" aria-label="Save">
                        <i class="fa-regular fa-bookmark"></i>
                      </button>
                    </form>
                    <?php
                  }
                }
                if (isset($_SESSION['username']) && $_SESSION['username'] == $postAuthor) {
                  ?>
                  <form method="post" action="index.php?do=delete" class="mx-1">
                    <input type="hidden" name="id" value="<?php echo $postId; ?>">
                    <button class="btn btn-danger" type="submit" aria-label="Delete">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </form>
                  <form method="post" action="index.php?do=edit">
                    <input type="hidden" name="id" value="<?php echo $postId; ?>">
                    <button class="btn btn-success" type="submit" aria-label="Edit">
                      <i class="fa-solid fa-edit"></i>
                    </button>
                  </form>
                  <?php
                }
                ?>
              </div>
            </li>
            <?php
          }
          ?>
        </ul>
      </div>
      <div class="col-md-4">
        <form class="d-flex" id="search-form" role="search">
          <input class="form-control me-2" type="search" placeholder="Enter article title" aria-label="Search"
            id="search-input" required="required" />
          <button class="btn btn-outline-success" type="submit" disabled aria-label="Submit">
            <i class="fa-solid fa-magnifying-glass"></i>
          </button>
        </form>
        <hr />
        <div id="search-results" class="mb-3"></div>
        <?php
        if (isset($_SESSION['username'])) {
          $user_id = $_SESSION['id'];
          $stmt = $con->prepare("SELECT `saved_articles`.`id`, `saved_articles`.`article_id`, `saved_articles`.`created_at`, `posts`.`title_p`, `posts`.`id` FROM `saved_articles` INNER JOIN `posts` ON `saved_articles`.`article_id` = `posts`.`id` WHERE `saved_articles`.`user_id` = ?");
          $stmt->execute([$user_id]);
          $AllSaved = $stmt->fetchAll(PDO::FETCH_ASSOC);
          echo '<h4><i class="fa-solid fa-bookmark"></i>&nbsp;Saved</h4>';
          if (!empty($AllSaved)) {
            foreach ($AllSaved as $save) {
              ?>
              <li class="nav-item text-bg-dark text-capitalize">
                <a class="nav-link position-relative text-truncate"
                  href="./posts/<?php echo sanitizeTitle($save['title_p']) . '.md'; ?>" target="_blank">
                  <?php echo $save['title_p']; ?>
                  <form class="position-absolute top-50 end-0 translate-middle-y" method="post" action="index.php?do=save">
                    <input type="hidden" name="id" value="<?php echo $save['id']; ?>">
                    <button class="btn btn-dark" type="submit" aria-label="Delete Saved">
                      <i class="fa-solid fa-bookmark"></i>
                    </button>
                  </form>
                </a>
              </li>
              <?php
            }
          } else {
            echo '<p class="text-muted">There are no articles saved</p>';
          }
        }
        $stmt = $con->prepare("SELECT `categories`, COUNT(*) AS article_count FROM `posts` GROUP BY `categories`");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo '<hr />';
        echo '<h4><i class="fa-solid fa-receipt"></i>&nbsp;Categories</h4>';
        echo '<ul>';
        foreach ($categories as $category) {
          echo '<li class="nav-item ms-4 d-flex justify-content-between">';
          echo '<a class="nav-link" href="category.php?category=' . $category['categories'] . '"><i class="fa-solid fa-play"></i>&nbsp;' . $category['categories'] . '</a>';
          echo '<span class="badge bg-dark">' . $category['article_count'] . '</span>';
          echo '</li>';
        }
        echo '</ul>';
        ?>
      </div>
    </div>
    <nav aria-label="Page navigation">
      <ul class="pagination justify-content-center mt-3">
        <?php if ($page > 1): ?>
          <li class="page-item">
            <a class="page-link" href="index.php?page=<?php echo $page - 1; ?>">Previous</a>
          </li>
        <?php else: ?>
          <li class="page-item disabled">
            <a class="page-link">Previous</a>
          </li>
        <?php endif;
        for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
            <a class="page-link" href="index.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor;
        if ($page < $totalPages): ?>
          <li class="page-item">
            <a class="page-link" href="index.php?page=<?php echo $page + 1; ?>">Next</a>
          </li>
        <?php else: ?>
          <li class="page-item disabled">
            <a class="page-link">Next</a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
  <?php
} elseif ($do == 'create-post') {
  if (isset($_SESSION['username'])) {
    if (isset($_SESSION['message'])): ?>
      <div id="message">
        <?php echo $_SESSION['message']; ?>
      </div>
      <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <div class="row">
      <div class="col-md-6 mx-auto">
        <form id="form-created" method="POST" action="index.php?do=create-true">
          <h1>Create New Articles</h1>
          <p class="text-muted">Write your article in Markdown format.</p>
          <div class="form-group mb-3">
            <label for="title_p">Title</label>
            <input type="text" class="form-control" name="title_p" required="required">
          </div>
          <div class="form-group mb-3">
            <label for="content_p">Content</label>
            <textarea class="form-control" name="content_p" required="required" rows="14"></textarea>
          </div>
          <div class="form-group mb-3">
            <label for="categories">Category</label>
            <input type="text" class="form-control" name="categories" required="required">
          </div>
          <button type="submit" class="btn btn-primary" disabled>Publish</button>
        </form>
      </div>
    </div>
    <?php
  } else {
    header('location: index.php');
    exit();
  }
} elseif ($do == 'enter') {
  if (isset($_SESSION['message'])): ?>
    <div id="message">
      <?php echo $_SESSION['message']; ?>
    </div>
    <?php unset($_SESSION['message']); ?>
  <?php endif; ?>
  <div class="row">
    <div class="col-md-6 mx-auto">
      <div class="enter text-md-center">
        <form class="form-enter rounded mt-5" action="index.php?do=enter-true" method="POST" autocomplete="off">
          <div class="sign-head text-uppercase">
            <h1>enter</h1>
          </div>
          <div class="input-group mb-3">
            <div class="form-floating">
              <input class="form-control" type="text" name="username" required="required" />
              <label>Username</label>
            </div>
          </div>
          <div class="input-group mb-3">
            <div class="form-floating">
              <input class="form-control" type="password" name="password" required="required" />
              <label>Password</label>
            </div>
          </div>
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary text-uppercase" disabled>Continue</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php
} elseif ($do == 'new-registration') {
  if (isset($_SESSION['message'])): ?>
    <div id="message">
      <?php echo $_SESSION['message']; ?>
    </div>
    <?php unset($_SESSION['message']); ?>
  <?php endif;
  ?>
  <div class="row">
    <div class="col-md-6 mx-auto">
      <div class="registration mb-3">
        <form class="form-reg rounded mt-5" action="index.php?do=insert-true" method="POST" autocomplete="off"
          enctype="multipart/form-data">
          <div class="registration-head text-uppercase">
            <h1>New Registration</h1>
          </div>
          <div class="form-group mb-3">
            <label for="username">Username<sub class="text-danger">*</sub></label>
            <input class="form-control" name="username" required="required">
          </div>
          <div class="form-group mb-3">
            <label for="fullname">Full Name<sub class="text-danger">*</sub></label>
            <input class="form-control" name="fullname" required="required">
          </div>
          <div class="form-group mb-3">
            <label for="email">Email<sub class="text-danger">*</sub></label>
            <input type="email" class="form-control" name="email" required="required">
          </div>
          <div class="form-group mb-3">
            <label for="password">Password<sub class="text-danger">*</sub></label>
            <input type="password" class="form-control" name="password" required="required">
          </div>
          <div class="form-group mb-3">
            <label for="avatar">Avatar</label>
            <input type="file" accept=".png" class="form-control" name="avatar">
          </div>
          <div class="form-group mb-3">
            <label for="city">City</label>
            <input class="form-control" name="city">
          </div>
          <div class="form-group mb-3">
            <label for="phone">Phone<sub class="text-danger">*</sub></label>
            <input type="number" class="form-control" name="phone" required="required">
          </div>
          <div class="form-group mb-3">
            <label for="bio">Bio</label>
            <textarea class="form-control" name="bio" rows="4"></textarea>
          </div>
          <button type="submit" class="btn btn-primary" disabled>Registration</button>
        </form>
      </div>
    </div>
  </div>
  <?php
} elseif ($do == 'edit') {
  if (isset($_SESSION['username'])) {
    $id = $_POST['id'];
    $stmt = $con->prepare("SELECT `title_p`, `content_p`, `author_p`, `categories`, `posting_p` FROM `posts` WHERE `id` = ? LIMIT 1");
    $stmt->execute(array($id));
    $postInfo = $stmt->fetch();
    if (isset($_SESSION['message'])): ?>
      <div id="message">
        <?php echo $_SESSION['message']; ?>
      </div>
      <?php unset($_SESSION['message']); ?>
    <?php endif;
    ?>
    <div class="row">
      <div class="col-md-6 mx-auto">
        <form class="form-edited" method="POST" action="index.php?do=update-post-true">
          <h1>Edit Articles :
            <?php echo $postInfo['title_p'] ?>
          </h1>
          <div class="form-group mb-3">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <label for="title_p">Title
              <sub class="text-danger">&nbsp;it is not recommended to modify the title of the
                article
              </sub>
            </label>
            <input type="text" class="form-control" name="title_p" value="<?php echo $postInfo['title_p'] ?>"
              required="required">
          </div>
          <div class="form-group mb-3">
            <label for="content_p">Content</label>
            <textarea class="form-control" name="content_p" required="required"
              rows="14"><?php echo $postInfo['content_p'] ?></textarea>
          </div>
          <div class="form-group mb-3">
            <label for="categories">Category</label>
            <input type="text" class="form-control" name="categories" value="<?php echo $postInfo['categories'] ?>"
              required="required">
          </div>
          <button type="submit" class="btn btn-primary">Update Post</button>
        </form>
      </div>
    </div>
    <?php
  } else {
    header('location: index.php');
    exit();
  }
} elseif ($do == 'save') {
  if (isset($_SESSION['username'])) {
    $articleId = $_POST['id'];
    $checkSaved = $con->prepare("SELECT COUNT(*) FROM saved_articles WHERE user_id = ? AND article_id = ?");
    $checkSaved->execute(array($_SESSION['id'], $articleId));
    $isSaved = ($checkSaved->fetchColumn() > 0);
    if ($isSaved) {
      $deleteSaved = $con->prepare("DELETE FROM saved_articles WHERE user_id = ? AND article_id = ?");
      $deleteSaved->execute(array($_SESSION['id'], $articleId));
      show_message('Article removed from saved articles!', 'success');
    } else {
      $saveArticle = $con->prepare("INSERT INTO saved_articles (user_id, article_id) VALUES (?, ?)");
      $saveArticle->execute(array($_SESSION['id'], $articleId));
      show_message('Article saved successfully!', 'success');
    }
    header('location: index.php');
    exit();
  } else {
    header('location: index.php');
    exit();
  }
} elseif ($do == 'delete') {
  if (isset($_SESSION['username'])) {
    $id = $_POST['id'];
    $getPostTitle = $con->prepare("SELECT `title_p` FROM `posts` WHERE `id` = ?");
    $getPostTitle->execute(array($id));
    $title = $getPostTitle->fetchColumn();
    $stmt = $con->prepare("DELETE FROM `posts` WHERE `id` = ?");
    $stmt->execute(array($id));
    if ($title) {
      $title = str_replace(' ', '-', $title);
      $markdownFilePath = './posts/' . $title . '.md';
      if (file_exists($markdownFilePath)) {
        unlink($markdownFilePath);
      }
    }
    show_message('The post has already been deleted.', 'success');
    header('Location: index.php');
    exit();
  } else {
    header('location: index.php');
    exit();
  }
} elseif ($do == 'create-true') {
  if (isset($_SESSION['username'])) {
    $title = $_POST['title_p'];
    $content = $_POST['content_p'];
    $category = $_POST['categories'];
    $author_p = $_SESSION['username'];
    $stmt = $con->prepare("INSERT INTO `posts`(`title_p`, `content_p`, `author_p`, `categories`, `posting_p`) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$title, $content, $author_p, $category]);
    $postID = $con->lastInsertId();
    $sanitizedTitle = sanitizeTitle($title);
    $filePath = './posts/' . $sanitizedTitle . '.md';
    if (!is_dir('posts')) {
      mkdir('posts');
    }
    $fileContent = "# " . $title . "\n\nAuthor: " . $author_p . "\n\n" . $content;
    file_put_contents($filePath, $fileContent);
    show_message('Post created successfully!', 'success');
    header('location: index.php?do=create-post');
    exit();
  } else {
    header('location: index.php');
    exit();
  }
} elseif ($do == 'update-post-true') {
  if (isset($_SESSION['username'])) {
    $id = $_POST['id'];
    $title = $_POST['title_p'];
    $content = $_POST['content_p'];
    $categories = $_POST['categories'];
    $author_p = $_SESSION['username'];
    $stmt = $con->prepare("UPDATE `posts` SET `title_p` = ?, `content_p` = ?, `categories` = ? WHERE `id` = ?");
    $stmt->execute([$title, $content, $categories, $id]);
    $sanitizedTitle = sanitizeTitle($title);
    $filePath = './posts/' . $sanitizedTitle . '.md';
    $fileContent = "# " . $title . "\n\nAuthor: " . $author_p . "\n\n" . $content;
    file_put_contents($filePath, $fileContent);
    show_message('Post updated successfully!', 'success');
    header('location: index.php');
    exit();
  } else {
    header('location: index.php');
    exit();
  }
} elseif ($do == 'insert-true') {
  if (empty($_POST['username']) || empty($_POST['fullname']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['phone'])) {
    show_message('Please fill in all fields', 'danger');
    header('location: index.php?do=new-registration');
    exit();
  }
  $username = $_POST['username'];
  $fullname = $_POST['fullname'];
  $email = $_POST['email'];
  $password = sha1($_POST['password']);
  $city = $_POST['city'];
  $phone = $_POST['phone'];
  $bio = $_POST['bio'];
  $upload_dir = './uploads/profiles/';
  $avatar = '';
  if (!empty($_FILES['avatar']['name'])) {
    $file_name = $_FILES['avatar']['name'];
    $file_tmp = $_FILES['avatar']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $avatar = uniqid() . '.' . $file_ext;
    list($width, $height) = getimagesize($file_tmp);
    if ($width !== $height) {
      show_message('The image dimensions must be equal.', 'danger');
      header('location: index.php?do=new-registration');
      exit();
    }
    if (move_uploaded_file($file_tmp, $upload_dir . $avatar)) {
    } else {
      show_message('Failed to upload avatar.', 'danger');
      header('location: index.php?do=new-registration');
      exit();
    }
  }
  $checkUsername = $con->prepare("SELECT COUNT(*) FROM `users` WHERE `username` = ?");
  $checkUsername->execute([$username]);
  $countUsername = $checkUsername->fetchColumn();
  $checkEmail = $con->prepare("SELECT COUNT(*) FROM `users` WHERE `email` = ?");
  $checkEmail->execute([$email]);
  $countEmail = $checkEmail->fetchColumn();
  if ($countUsername > 0) {
    show_message('Username already exists. Please choose a different username.', 'danger');
    header('location: index.php?do=new-registration');
    exit();
  } elseif ($countEmail > 0) {
    show_message('Email address already exists. Please choose a different email address.', 'danger');
    header('location: index.php?do=new-registration');
    exit();
  } else {
    $stmt = $con->prepare("INSERT INTO `users`(`username`, `fullname`, `email`, `password`, `avatar`, `city`, `phone`, `bio`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$username, $fullname, $email, $password, $avatar, $city, $phone, $bio]);
    if ($stmt->rowCount() > 0) {
      show_message('Registration successful.', 'success');
      header('location: index.php?do=enter');
      exit();
    } else {
      show_message('Failed to insert data.', 'danger');
      header('location: index.php?do=new-registration');
      exit();
    }
  }
} elseif ($do == 'enter-true') {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $hashedPass = sha1($password);
  $stmt = $con->prepare("SELECT `id`, `username`, `password` FROM `users` WHERE `username` = ? AND `password` = ?");
  $stmt->execute(array($username, $hashedPass));
  $row = $stmt->fetch();
  $count = $stmt->rowCount();
  if ($count > 0) {
    $_SESSION['username'] = $username; // Register Session Name
    $_SESSION['id'] = $row['id']; // Register Session ID
    header('location: index.php');
    exit();
  } else {
    show_message('Sorry, You must make sure that the information entered is correct', 'danger');
    header('location: index.php?do=enter');
    exit();
  }
}

include $tpl . 'footer.php'; ?>