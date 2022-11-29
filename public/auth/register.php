<?php
session_start();
include(__DIR__.'/../../sql.php');

$conn = new Sql();

if(isset($_SESSION['user_id'])){
    $conn->close();
    header('Location: /');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $_POST['name'] = trim($_POST['name']);
    $user = $conn->query("SELECT * FROM users WHERE name = ? ", [$_POST['name']]);
    if($user->rowCount() == 0) {
        $userId = $conn->insert("INSERT INTO users (name, password) VALUES (?, ?)", [$_POST['name'], $_POST['password']]);
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $_POST['name'];
        $_POST[] = [];
        $conn->close();
        header('Location: /');
    } else {
        $_POST[] = [];
        $conn->close();
        setcookie('error', 'The name is already taken.', time() + 3600);
        header('Location: /auth/register.php');
    }
    exit;
}

include(__DIR__.'/../../Render/PageRender.php');

$render = new Render\PageRender();
$render->setTitle('Register');

$error = '';
if(isset($_COOKIE['error'])) {
    $error = <<<EOT
    <p style="color: red;">{$_COOKIE['error']}</p>
    EOT;
    setcookie('error', '', time() - 3600);
}

$render->setHeader([
    [
        'name' => 'Back',
        'url' => '/'
    ],
    [
        'name' => 'Login',
        'url' => '/auth/login.php'
    ],
]);
$render->setBody(<<<EOT
{$error}
<form method="post" class="flex flex-col items-center justify-center space-y-5">
    <input type="text" name="name" placeholder="Name" class="w-full rounded p-2 ring-1 ring-green-800" max="45" required>
    <input type="password" name="password" placeholder="Password" class="w-full rounded p-2 ring-1 ring-green-800" required>
    <input type="submit" value="Register">
</form>
EOT);

echo $render->rend(); 
$conn->close();
