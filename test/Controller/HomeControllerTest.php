<?php

namespace Akmalmp\BelajarPhpMvc\Controller;

use Akmalmp\BelajarPhpMvc\Config\Database;
use Akmalmp\BelajarPhpMvc\Domain\Session;
use Akmalmp\BelajarPhpMvc\Domain\User;
use Akmalmp\BelajarPhpMvc\Repository\SessionRepository;
use Akmalmp\BelajarPhpMvc\Repository\UserRepository;
use Akmalmp\BelajarPhpMvc\Service\SessionService;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
{
    private HomeController $homeController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->homeController = new HomeController();
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testGuest()
    {
        $this->homeController->index();

        $this->expectOutputRegex("[Login Management]");
    }

    public function testUserLogin()
    {
        $user = new User();
        $user->setId("akmp");
        $user->setName("Akmal");
        $user->setPassword("123456789");
        $this->userRepository->save($user);

        $session = new Session();
        $session->setId(uniqid());
        $session->setUserId($user->getId());
        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::getCOOKIENAME()] = $session->getId();

        $this->homeController->index();

        $this->expectOutputRegex("[Hello Akmal]");
    }


}
