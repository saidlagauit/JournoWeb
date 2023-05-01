<?php
session_start();
$noNavbar = '';
include 'init.php';
$searchTerm = $_POST['search'];
$lowerSearchTerm = strtolower($searchTerm);
$stmt = $con->prepare("SELECT `id`, `title_p`, `content_p`, `author_p`, `posting_p` FROM `posts` WHERE LOWER(`title_p`) LIKE ?");
$stmt->execute(["%$lowerSearchTerm%"]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($results) > 0) {
  foreach ($results as $result) {
    ?>
    <div class="search-result text-bg-light text-capitalize">
      <div class="card-body">
        <h5 class="card-title py-1">
          <a href="./posts/<?php echo sanitizeTitle($result['title_p']) . '.md'; ?>" target="_blank"><?php echo $result['title_p']; ?></a>
        </h5>
      </div>
    </div>
    <?php
  }
} else {
  echo "<p>No results found.</p>";
}
?>