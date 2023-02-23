<?php

namespace Akmalmp\BelajarPhpMvc\App;

class View
{
    public static function  render(string $view, $model): void
    {
        require __DIR__ . '/../view/header.php';
        require __DIR__ . '/../view/' . $view . '.php';
        require __DIR__ . '/../view/footer.php';
    }

    public static function redirect(string $url): void
    {
        header("Location: $url");
        if (getenv("mode") != "test") {
            exit();
        }
    }
}