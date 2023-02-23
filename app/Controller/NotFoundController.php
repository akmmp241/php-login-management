<?php

namespace Akmalmp\BelajarPhpMvc\Controller;

use Akmalmp\BelajarPhpMvc\App\View;

class NotFoundController
{
    public static function notFound(): void
    {
        View::render("NotFound/not-found", [
            'title' => '404 NOT FOUND',
            'error' => '404 - ERROR CODE (PAGE NOT FOUND)'
        ]);
    }
}