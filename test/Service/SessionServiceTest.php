<?php

namespace Akmalmp\BelajarPhpMvc\Service;

require_once __DIR__ . '/../Helper/helper.php';

use Akmalmp\BelajarPhpMvc\Config\Database;
use Akmalmp\BelajarPhpMvc\Domain\Session;
use Akmalmp\BelajarPhpMvc\Domain\User;
use Akmalmp\BelajarPhpMvc\Repository\SessionRepository;
use Akmalmp\BelajarPhpMvc\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class SessionServiceTest extends TestCase
{
    private SessionService $sessionService;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {

        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);


        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->setId("akm");
        $user->setName("Akmal");
        $user->setPassword("rahasia");
        $this->userRepository->save($user);
    }

    public function testCreate()
    {
        $session = $this->sessionService->create("akm");

        $this->expectOutputRegex("[X-AKM-SESSION: {$session->getId()}]");

        $result = $this->sessionRepository->findById($session->getId());

        self::assertEquals($session->getUserId(), $result->getUserId());
    }

    public function testDestroy()
    {
        $session = new Session();
        $session->setId(uniqid());
        $session->setUserId("akm");

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::getCOOKIENAME()] = $session->getId();

        $this->sessionService->destroy();

        $this->expectOutputRegex("[X-AKM-SESSION: ]");

        $result = $this->sessionRepository->findById($session->getId());

        self::assertNull($result);
    }

    public function testCurrent()
    {
        $session = new Session();
        $session->setId(uniqid());
        $session->setUserId("akm");

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::getCOOKIENAME()] = $session->getId();

        $user = $this->sessionService->current();

        self::assertEquals($session->getUserId(), $user->getId());
    }
}
