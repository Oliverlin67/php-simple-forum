<?php
session_start();
include(__DIR__.'/../sql.php');

$conn = new Sql();
$articles = $conn->query("SELECT articles.id, articles.title, users.name FROM articles INNER JOIN users ON articles.user_id=users.id");
$conn->close();

$actions = [
    [
        'name' => 'Login',
        'url' => '/auth/login.php'
    ],
    [
        'name' => 'Register',
        'url' => '/auth/register.php'
    ],
];

if(isset($_SESSION['user_id'])) {
    $actions = [
        [
            'name' => 'New Article',
            'url' => '/articles/create.php'
        ],
        [
            'name' => 'Log Out',
            'url' => 'logout.php'
        ],
    ];
}

$contents = "";
if($articles->rowCount() > 0) {
    foreach ($articles as $article) {
        $name = htmlspecialchars($article['name'], ENT_QUOTES);
        $title = htmlspecialchars($article['title'], ENT_QUOTES);
        $contents .= <<<EOT
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
    $contents = <<<EOT
    <li class="flex flex-col items-center jusitfy-center m-8">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-48 h-48 text-green-700">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
        </svg>
        <p class="text-green-800 text-2xl font-black">No Articles</p>
    </li>
    EOT;
}

include(__DIR__.'/../Render/PageRender.php');

$render = new Render\PageRender();
$render->setTitle('Index');
$render->setHeader($actions);

$render->setBody(<<<EOT
<ul class="space-y-3">
{$contents}
</ul>
EOT);

echo $render->rend();
