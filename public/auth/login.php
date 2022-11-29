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
    $user = $conn->query("SELECT * FROM users WHERE name = ? AND password = ?", [$_POST['name'], $_POST['password']]);
    if($user->rowCount() == 1) {
        $user = $user->fetch();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_POST[] = [];
        $conn->close();
        header('Location: /');
    } else {
        $_POST[] = [];
        $conn->close();
        setcookie('error', 'The name or password is incorrect.', time() + 3600);
        header('Location: /auth/login.php');
    }
    exit;
}

include(__DIR__.'/../../Render/PageRender.php');

$render = new Render\PageRender();
$render->setTitle('Login');

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
        'name' => 'Register',
        'url' => '/auth/register.php'
    ]
]);
$render->setBody(<<<EOT
{$error}
<form method="post" class="flex flex-col items-center justify-center space-y-5">
    <input type="text" name="name" placeholder="Name" class="w-full rounded p-2 ring-1 ring-green-800" required>
    <input type="password" name="password" placeholder="Password" class="w-full rounded p-2 ring-1 ring-green-800" required>
    <input type="submit" value="Login">
</form>
EOT);

echo $render->rend(); 
$conn->close();
?>