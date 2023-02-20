<?php

namespace Akmalmp\BelajarPhpMvc\Repository;

use Akmalmp\BelajarPhpMvc\Config\Database;
use Akmalmp\BelajarPhpMvc\Domain\Session;
use Akmalmp\BelajarPhpMvc\Domain\User;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;
use const http\Client\Curl\Features\UNIX_SOCKETS;

class SessionRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->setId("akm");
        $user->setName("Akmal");
        $user->setPassword("rahasia");
        $this->userRepository->save($user);
    }

    public function testSaveSuccess()
    {
        $sessions = new Session();
        $sessions->setId(uniqid());
        $sessions->setUserId("akm");

        $this->sessionRepository->save($sessions);

        $result = $this->sessionRepository->findById($sessions->getId());

        assertEquals($sessions->getId(), $result->getId());
        assertEquals($sessions->getUserId(), $result->getUserId());
    }

    public function testDeleteByIdSuccess()
    {
        $sessions = new Session();
        $sessions->setId(uniqid());
        $sessions->setUserId("akm");

        $this->sessionRepository->save($sessions);

        $result = $this->sessionRepository->findById($sessions->getId());

        $this->sessionRepository->deleteById($sessions->getId());

        $result = $this->sessionRepository->findById($sessions->getId());

        self::assertNull($result);
    }

    public function testFindByIdNotFound()
    {
        $result = $this->sessionRepository->findById("notfound");
        self::assertNull($result);
    }


}
