<?php

namespace Akmalmp\BelajarPhpMvc\Repository;

use Akmalmp\BelajarPhpMvc\Config\Database;
use Akmalmp\BelajarPhpMvc\Domain\User;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{

    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();

        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
    }


    public function testSaveSuccess()
    {
        $user = new User();
        $user->setId("akmal");
        $user->setName("Akmal Muhamamd P");
        $user->setPassword("12345");

        $this->userRepository->save($user);

        $result = $this->userRepository->findByID($user->getId());

        self::assertEquals($user->getId(), $result->getId());
        self::assertEquals($user->getName(), $result->getName());
        self::assertEquals($user->getPassword(), $result->getPassword());
    }

    public function testFindByIdNotFound()
    {
        $user = $this->userRepository->findByID("notfound");
        self::assertNull($user);
    }

    public function testUpdate()
    {
        $user = new User();
        $user->setId("akm");
        $user->setName("Akmal");
        $user->setPassword("sahabatku");
        $this->userRepository->save($user);

        $user->setName("akmalmp");
        $this->userRepository->update($user);

        $result = $this->userRepository->findByID($user->getId());

        self::assertEquals($user->getId(), $result->getId());
        self::assertEquals($user->getName(), $result->getName());
        self::assertEquals($user->getPassword(), $result->getPassword());
    }


}
