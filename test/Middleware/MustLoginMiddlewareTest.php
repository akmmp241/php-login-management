<?php

namespace Akmalmp\BelajarPhpMvc\Middleware {

    require_once __DIR__ . '/../Helper/helper.php';

    use Akmalmp\BelajarPhpMvc\Config\Database;
    use Akmalmp\BelajarPhpMvc\Domain\Session;
    use Akmalmp\BelajarPhpMvc\Domain\User;
    use Akmalmp\BelajarPhpMvc\Repository\SessionRepository;
    use Akmalmp\BelajarPhpMvc\Repository\UserRepository;
    use Akmalmp\BelajarPhpMvc\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class MustLoginMiddlewareTest extends TestCase
    {
        private MustLoginMiddleware $mustLoginMiddleware;
        private SessionRepository $sessionRepository;
        private UserRepository $userRepository;

        protected function setUp(): void
        {
            $this->mustLoginMiddleware = new MustLoginMiddleware();
            putenv("mode=test");

            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->userRepository = new UserRepository(Database::getConnection());

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }

        public function testBeforeGuest()
        {
            $this->mustLoginMiddleware->before();
            $this->expectOutputRegex("[Location: /users/login]");
        }

        public function testBeforeLoginUser()
        {
            $user = new User();
            $user->setId("akm");
            $user->setName("Akmal Mp");
            $user->setPassword(password_hash("sahabatku", PASSWORD_BCRYPT));
            $this->userRepository->save($user);

            $session = new Session();
            $session->setId(uniqid());
            $session->setUserId($user->getId());
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::getCOOKIENAME()] = $session->getId();


            $this->mustLoginMiddleware->before();
            $this->expectOutputString("");
        }
    }
}