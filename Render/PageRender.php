<?php

namespace Render;

class PageRender {
    private $noPageTitle;
    private $title;
    private $body;
    private $header;

    public function __construct($noPageTitle = false)
    {
        $this->noPageTitle = $noPageTitle;
        $this->title = '';
        $this->body = '';
    }

    public function setTitle($value) {
        $this->title = $value;
    }

    public function setBody($value) {
        $this->body = $value;
    }

    public function setHeader($items = []) {
        foreach($items as $item) {
            $this->header .= <<<EOL
            <a href="{$item['url']}">{$item['name']}</a>
            EOL;
        }
    }

    public function rend() {
        $body = <<<EOT
            <p class="my-4 text-2xl">
                {$this->title}
            </p>
            <div>
                {$this->body}
            </div>
        EOT;

        if($this->noPageTitle) {
            $body = <<<EOT
            <div class="my-4 text-2xl">
                {$this->body}
            </div>
            EOT;
        }

        $current_user = isset($_SESSION['user_id']) && isset($_SESSION['user_name']) ? "Current User: <a href=\"/profile.php?id=".$_SESSION['user_id']."\">".$_SESSION['user_name']."</a>" : "";

        return <<<EOT
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Simple Forum | {$this->title}</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body>
            <div class="w-screen h-12 bg-green-200 text-green-800">
                <div class="flex items-center space-x-3 h-full max-w-4xl mx-auto">
                    {$this->header}
                    <div class="flex-1 flex items-center justify-end">
                        <div class="">{$current_user}</div>
                    </div>
                </div>
            </div>
            <div class="max-w-4xl mx-auto">
                {$body}
            </div>
        </body>
        </html>
        EOT;
    }
}