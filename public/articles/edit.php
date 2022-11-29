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

$article = $article->fetch();

if($article['user_id'] != $_SESSION['user_id']) {
    include_once(__DIR__.'/../../errors.php');
    error_page_render(403, 'You are not allowed to edit this article.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = $_POST['title'];
    $content = $_POST['content'];
    $conn->update("UPDATE articles SET title = ?, content = ? WHERE (id = ?);", [filter_var($title,FILTER_SANITIZE_SPECIAL_CHARS), filter_var($content,FILTER_SANITIZE_SPECIAL_CHARS), $_GET['id']]);
    $_POST[] = [];
    $conn->close();
    header('Location: /articles/show.php?id='.$_GET['id']);
    exit;
}

include(__DIR__.'/../../Render/PageRender.php');

$render = new Render\PageRender();
$render->setTitle("Editing {$article['title']}");
$render->setHeader([[
    'name' => 'Back',
    'url' => '/articles/show.php?id='.$article['id']
]]);

$render->setBody(<<<EOT
<form method="post" class="flex flex-col space-y-5">
    <div>
        <label for="title">Title</label>
        <input type="text" id="title" name="title" placeholder="Title" value="{$article['title']}" class="w-full rounded p-2 ring-1 ring-green-800" required>
    </div>
    <div>
        <label for="content">Content</label>
        <textarea name="content" id="content" placeholder="Content" class="w-full rounded p-2 ring-1 ring-green-800" rows="10" required>{$article['content']}</textarea>
    </div>
    <button type="submit" class="rounded px-4 py-3 bg-green-800 text-green-50">
    Update
    </button>
</form>
EOT);

echo $render->rend();
$conn->close();