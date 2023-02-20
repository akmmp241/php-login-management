<?php

namespace Akmalmp\BelajarPhpMvc\Middleware;

use Akmalmp\BelajarPhpMvc\App\View;
use Akmalmp\BelajarPhpMvc\Config\Database;
use Akmalmp\BelajarPhpMvc\Repository\SessionRepository;
use Akmalmp\BelajarPhpMvc\Repository\UserRepository;
use Akmalmp\BelajarPhpMvc\Service\SessionService;

class MustNotLoginMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $userRepository = new UserRepository(Database::getConnection());
        $sessionRepository = new SessionRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    function before(): void
    {
        $user = $this->sessionService->current();
        if ($user != null) {
            View::redirect('/');
        }
    }
}