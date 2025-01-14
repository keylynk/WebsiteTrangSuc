<?php
// Update the details below with your MySQL details
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'bantrangsuc';
try {
    $pdo = new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
} catch (PDOException $exception) {
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to database!');
}

// Below function will convert datetime to time elapsed string.
date_default_timezone_set('Asia/Ho_Chi_Minh');
function time_elapsed_string($datetime, $full = false) {
  $now = new DateTime;
  $ago = new DateTime($datetime);
  $diff = $now->diff($ago);
  $w = floor($diff->d / 7);
  $diff->d -= $w * 7;
  $string = ['y' => 'year','m' => 'month','w' => 'week','d' => 'day','h' => 'hour','i' => 'minute','s' => 'second'];
  foreach ($string as $k => &$v) {
      if ($k == 'w' && $w) {
          $v = $w . ' week' . ($w > 1 ? 's' : '');
      } else if (isset($diff->$k) && $diff->$k) {
          $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
      } else {
          unset($string[$k]);
      }
  }
  if (!$full) $string = array_slice($string, 0, 1);
  return $string ? implode(', ', $string) . ' ago' : 'just now';
}


// Page ID needs to exist, this is used to determine which reviews are for which page.
if (isset($_GET['page_id'])) {
  if (isset($_POST['name'], $_POST['rating'], $_POST['content'])) {
      // Insert a new review (user submitted form)
      $stmt = $pdo->prepare('INSERT INTO reviews (page_id, name, content, rating, submit_date) VALUES (?,?,?,?,NOW())');
      $stmt->execute([$_GET['page_id'], $_POST['name'], $_POST['content'], $_POST['rating']]);
      exit('Đánh giá của bạn đã được gửi!');
  }
  // Get all reviews by the Page ID ordered by the submit date
  $stmt = $pdo->prepare('SELECT * FROM reviews WHERE page_id = ? ORDER BY submit_date DESC');
  $stmt->execute([$_GET['page_id']]);
  $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // Get the overall rating and total amount of reviews
  $stmt = $pdo->prepare('SELECT AVG(rating) AS overall_rating, COUNT(*) AS total_reviews FROM reviews WHERE page_id = ?');
  $stmt->execute([$_GET['page_id']]);
  $reviews_info = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
  exit('Vui lòng cung cấp ID trang.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cửa hàng</title>
    <!--fonts-->
    <link rel="stylesheet" href="fonts/rougescript.css">
    <!--script swiper-->
    <link rel="stylesheet" href="css/swiper.min.css">
    <!--css-->
    <link rel="stylesheet" href="css/reviews.css">

    <!-- icon and title -->
    <link rel="shortcut icon" href="images/favicon.ico" />
</head>
<body>
    <div class="overall_rating">
        <span class="num"><?=number_format($reviews_info['overall_rating'], 1)?></span>
        <span class="stars"><?=str_repeat('&#9733;', round($reviews_info['overall_rating']))?></span>
        <span class="total"><?=$reviews_info['total_reviews']?> đánh giá</span>
    </div>
    <a href="#" class="write_review_btn">Viết đánh giá</a>
    <div class="write_review">
        <form>
            <input name="name" type="text" placeholder="Tên của bạn" required>
            <input name="rating" type="number" min="1" max="5" placeholder="Xếp hạng (1-5)" required>
            <textarea name="content" placeholder="Viết đánh giá của bạn ở đây..." required></textarea>
            <button type="submit">Gửi đánh giá</button>
        </form>
    </div>
</body>
</html>

<?php foreach ($reviews as $review): ?>
<div class="review">
    <h3 class="name"><?=htmlspecialchars($review['name'], ENT_QUOTES)?></h3>
    <div>
        <span class="rating"><?=str_repeat('&#9733;', $review['rating'])?></span>
        <span class="date"><?=time_elapsed_string($review['submit_date'])?></span>
    </div>
    <p class="review__content"><?=htmlspecialchars($review['content'], ENT_QUOTES)?></p>
</div>
<?php endforeach ?>