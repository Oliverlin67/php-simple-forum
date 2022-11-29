<?php
session_start();
include(__DIR__.'/../../sql.php');

$conn = new Sql();

if(!isset($_GET['id'])) {
    header('Location: /');
    exit;
}

$article = $conn->query("SELECT * FROM articles WHERE id = ?", [$_GET['id']]);

if($article->rowCount() != 1) {
    include_once(__DIR__.'/../../Render/errors.php');
    error_page_render(404, 'Article not found.');
}

$article = $article->fetch();

include(__DIR__.'/../../Render/PageRender.php');

$render = new Render\PageRender(true);
$render->setTitle($article['title']);

$author = $conn->query("SELECT * FROM users WHERE id = ?", [$article['user_id']])->fetch();
$likes = $conn->query("SELECT * FROM likes WHERE article_id = ?", [$_GET['id']])->rowCount();
$likeButton = <<<EOT
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
</svg>
<a href="/articles/like.php?id={$article['id']}">Like($likes)</a>
EOT;

$render->setHeader([[
    'name' => 'Back',
    'url' => '/'
]]);
if(isset($_SESSION['user_id'])) {
    if($_SESSION['user_id'] == $article['user_id']) {
        $render->setHeader([[
            'name' => 'Edit',
            'url' => '/articles/edit.php?id='.$article['id']
        ]]);
    }

    $liked = $conn->query("SELECT * FROM likes WHERE article_id = ? AND user_id = ?", [$_GET['id'], $_SESSION['user_id']]);

    if($liked->rowCount() == 1) {
        $likeButton =  <<<EOT
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
            <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
        </svg>
        <a href="/articles/like.php?id={$article['id']}">Unlike($likes)</a>
        EOT;
    }
}

$count = mb_strlen($article['content'], 'utf-8');

$render->setBody(<<<EOT
<article>
<h1 class="text-4xl font-black">{$article['title']}</h1>
<p class="text-lg text-black/70">Author: <a href="/profile.php?id={$author['id']}" class="text-green-600/100">{$author['name']}</a></p>
<div class="my-2 rounded p-5 bg-green-50 text-green-900 leading-relaxed">
    <small>({$count} character)</small>
    <br>
    {$article['content']}
</div>
<div class="flex items-center">
    {$likeButton}
</div>
</article>
EOT);

echo $render->rend(); 
$conn->close();
?>