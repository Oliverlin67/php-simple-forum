<?php
session_start();
include(__DIR__.'/../../sql.php');

$conn = new Sql();

if(!isset($_SESSION['user_id'])){
    $conn->close();
    header('Location: /auth/login.php');
    exit;
}

if(!isset($_GET['id'])) {
    $conn->close();
    header('Location: /');
    exit;
}

$article = $conn->query("SELECT * FROM articles WHERE id = ?", [$_GET['id']]);

if($article->rowCount() != 1) {
    include_once(__DIR__.'/../../errors.php');
    error_page_render(404, 'Article not found.');
}

$liked = $conn->query("SELECT * FROM likes WHERE article_id = ? AND user_id = ?", [$_GET['id'], $_SESSION['user_id']])->rowCount();
if($liked) {
    $conn->delete("DELETE FROM likes WHERE article_id = ? AND user_id = ?", [$_GET['id'], $_SESSION['user_id']]);
} else {
    $conn->insert("INSERT INTO likes (article_id, user_id) VALUES (?, ?)", [$_GET['id'], $_SESSION['user_id']]);
}

$conn->close();
header('Location: /articles/show.php?id='.$_GET['id']);