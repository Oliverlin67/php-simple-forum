<?php
session_start();
include(__DIR__.'/../../sql.php');

$conn = new Sql();

if(!isset($_SESSION['user_id'])){
    $conn->close();
    header('Location: /auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author = $_SESSION['user_id'];
    $id = $conn->insert("INSERT INTO articles (title, content, user_id) VALUES (?, ?, ?)", [filter_var($title,FILTER_SANITIZE_SPECIAL_CHARS), filter_var($content,FILTER_SANITIZE_SPECIAL_CHARS), $author]);
    $_POST[] = [];
    $conn->close();
    header('Location: /articles/show.php?id='.$id);
    exit;
}

include(__DIR__.'/../../Render/PageRender.php');

$render = new Render\PageRender();
$render->setTitle("Create New Article");
$render->setHeader([[
    'name' => 'Back',
    'url' => '/'
]]);

$render->setBody(<<<EOT
<form method="post" class="flex flex-col space-y-5">
    <div>
        <label for="title">Title</label>
        <input type="text" id="title" name="title" placeholder="Title" class="w-full rounded p-2 ring-1 ring-green-800" required>
    </div>
    <div>
        <label for="content">Content</label>
        <textarea name="content" id="content" placeholder="Content" class="w-full rounded p-2 ring-1 ring-green-800" rows="10" required></textarea>
    </div>
    <button type="submit" class="rounded px-4 py-3 bg-green-800 text-green-50">
    Create
    </button>
</form>
EOT);

echo $render->rend();
$conn->close();