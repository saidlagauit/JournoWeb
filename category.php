<?php
session_start();
include 'init.php';
if (!isset($_GET['category'])) {
  header('Location: index.php');
  exit();
}
$category = $_GET['category'];
$stmt = $con->prepare("SELECT `id`, `title_p`, `content_p`, `author_p`, `posting_p` FROM `posts` WHERE `categories` = ?");
$stmt->execute([$category]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="row">
  <div class="col-md-6 mx-auto">
    <div class="category mt-2">
      <ul>
        <?php foreach ($posts as $post): ?>
          <li class="nav-item ">
            <a class="nav-link my-1" href="./posts/<?php echo sanitizeTitle($post['title_p']) . '.md'; ?>">
              <?php echo $post['title_p']; ?>
              <sub class="text-bg-dark rounded p-1">
                <?php echo $post['posting_p']; ?>
              </sub>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>
<?php include $tpl . 'footer.php'; ?>