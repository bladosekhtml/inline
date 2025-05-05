<?php

function fill($conn)
{
  $posts = 'https://jsonplaceholder.typicode.com/posts';
  $comments = 'https://jsonplaceholder.typicode.com/comments';

  $context = stream_context_create([
    'http' => [
      'header' => 'Content-Type: application/json',
    ]
  ]);

  $posts_values = json_decode(file_get_contents($posts, false, $context));
  $comments_values = json_decode(file_get_contents($comments, false, $context));

  if (!$posts_values || !$comments_values) {
    die('Ошибка получения данных');
  }

  $posts_fill = 0;
  $comments_fill = 0;
  try {
    pg_query($conn, 'begin');

    if (is_array($posts_values) && count($posts_values)) {
      foreach ($posts_values as $post) {
        $result = pg_query_params(
          $conn,
          'INSERT INTO posts (id, userId, title, body) VALUES ($1, $2, $3, $4) ON CONFLICT (id) DO NOTHING',
          [
            $post->id,
            $post->userId,
            $post->title,
            $post->body,
          ]
        );

        if ($result && pg_affected_rows($result) > 0)
        {
          $posts_fill++;
        }
      }

      if (is_array($comments_values) && count($comments_values)) {
        foreach ($comments_values as $comment) {
          $result = pg_query_params(
            $conn,
            'INSERT INTO comments (id, postId, name, email, body) VALUES ($1, $2, $3, $4, $5) ON CONFLICT (id) DO NOTHING',
            [
              $comment->id,
              $comment->postId,
              $comment->name,
              $comment->email,
              $comment->body,
            ]
          );

          if ($result && pg_affected_rows($result) > 0)
        {
          $comments_fill++;
        }
        }

      }

      pg_query($conn, "commit");

      ?>
      <script>
        console.log('Загружено <?=$posts_fill?> записей и <?=$comments_fill?> комментариев');
      </script>
      <?php

      return true;
    }
  } catch (Exception $e) {
    pg_query($conn, 'rollback;');
    ?>
      <script>
        console.error('Ошибка');
      </script>
      <?php
    return false;
  }
}

function find($conn, $value)
{
  $result = pg_fetch_all(pg_query_params($conn, '
    SELECT posts.title, comments.body FROM comments JOIN posts on posts.id = comments.postId where comments.body like $1
  ', ["%$value%"]));

  return $result ?? [];
}