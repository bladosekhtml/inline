<?php
require_once('functions.php');

$dbname = 'tz1';
$user = 'postgres';
$password = 'vlad2002d';

$conn = pg_connect("host=127.0.0.1 dbname=$dbname user=$user password=$password");

if (!$conn) {
  die('Ошибка подключени к базе данных');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  fill($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <form method='post'>
    <button type='submit' name='fill'>
      Загрузить список записей и коментариев
    </button>
  </form>

  <br>

  <form method='get'>
    <h3>Поиск</h3>
    <input type="text" placeholder="Поиск" minlength="3" name = 'value'>
    <button type='submit'>
      Найти
    </button>
  </form>

  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['value']) && strlen($_GET['value']) > 2) {
    $values = find($conn, $_GET['value']);

    if ($values) {
      ?>
      <ol>
        <?php
          foreach ($values as $value) {
            ?>
            <li>
              title: <?=$value['title']?>
              <br>
              comment: <?=$value['body']?>
            </li>
            <br>
            <?php
          }
        ?>
      </ol>
      <?php
    } else {
      ?>
      <h3>Записей не найдено</h3>
      <?php
    }
  }
  ?>
</body>

</html>