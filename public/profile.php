<?php
session_start();
include(__DIR__.'/../sql.php');

$conn = new Sql();

if(!isset($_GET['id'])) {
    $conn->close();
    header('Location: /');
    exit;
}

$user = $conn->query("SELECT * FROM users WHERE id = ?", [$_GET['id']]);

if($user->rowCount() != 1) {
    include_once(__DIR__.'/../Render/errors.php');
    error_page_render(404, 'User not found.');
}

$articles = $conn->query("SELECT * FROM articles WHERE user_id = ?", [$_GET['id']]);
$user = $user->fetch();

$articleList = "";
if($articles->rowCount() > 0) {
    foreach ($articles as $article) {
        $name = htmlspecialchars($user['name'], ENT_QUOTES);
        $title = htmlspecialchars($article['title'], ENT_QUOTES);
        $articleList .= <<<EOT
        <li>
            <a href="/articles/show.php?id={$article['id']}">
                <div class="rounded p-4 bg-green-50 text-green-900">
                    <h1 class="text-3xl font-bold">{$title}</h1>
                    <p class="text-lg">Author: {$name}</p>
                </div>
            </a>
        </li>
        EOT;
    }
} else {
    $articleList = <<<EOT
    <li class="flex flex-col items-center jusitfy-center m-8">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-48 h-48 text-green-700">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
        </svg>
        <p class="text-green-800 text-2xl font-black">No Articles</p>
    </li>
    EOT;
}

include(__DIR__.'/../Render/PageRender.php');

$render = new Render\PageRender(true);
$render->setTitle("@".$user['name']);
$render->setHeader([
    [
        'name' => 'Back',
        'url' => '/'
    ],
]);
$render->setBody(<<<EOT
<div class="p-5 flex items-center space-x-5">
    <div class="h-32 w-32 rounded-full font-black text-5xl bg-green-800 text-green-50 flex items-center justify-center">
        {$user['name'][0]}
    </div>
    <div class"flex item-center">
        <h1 class="text-4xl font-bold">{$user['name']}</h1>
        <small>User ID: {$_GET['id']} | {$articles->rowCount()} Article</small>
    </div>
</div>
<div class="my-2 p-5 bg-green-200 rounded-lg relative">
    <h2 class="text-2xl rounded-t-lg bg-green-100 text-green-800 absolute top-0 left-0 w-full py-2 px-3 border-b-2 border-green-800">Articles</h2>
    <ul class="space-y-3 mt-14">
        {$articleList}
    </ul>
</div>
EOT);

echo $render->rend(); 
$conn->close();