<?php

function error_page_render($code, $message) {
    http_response_code($code);
?>

    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Simple Forum | <?= $message ?></title>
    </head>
    <body>
        <h1><?= $code ?></h1>
        <p><?= $message ?></p>
    </body>
    </html>

<?php
    die();
}