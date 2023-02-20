<?php
namespace Akmalmp\BelajarPhpMvc\Controller {

    use Akmalmp\BelajarPhpMvc\Config\Database;
    use Akmalmp\BelajarPhpMvc\Domain\Session;
    use Akmalmp\BelajarPhpMvc\Domain\User;
    use Akmalmp\BelajarPhpMvc\Exception\ValidationException;
    use Akmalmp\BelajarPhpMvc\Model\UserPasswordUpdateRequest;
    use Akmalmp\BelajarPhpMvc\Repository\SessionRepository;
    use Akmalmp\BelajarPhpMvc\Repository\UserRepository;
    use Akmalmp\BelajarPhpMvc\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void
        {
            $this->userController = new UserController();

            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();

            putenv("mode=test");
        }


        public function testRegister()
        {
            $this->userController->register();

            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Register New User]');
        }

        public function testPostRegisterSuccess()
        {
            $_POST['id'] = 'akm';
            $_POST['name'] = 'Akmal Muhammad P';
            $_POST['password'] = '12345678';

            $this->userController->postRegister();

            $this->expectOutputRegex("[Location: /users/login]");
        }

        public function testPostRegisterValidateError()
        {
            $_POST['id'] = '';
            $_POST['name'] = 'Akmal Muhammad P';
            $_POST['password'] = '1212121121';

            $this->userController->postRegister();

            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Register New User]');
            $this->expectOutputRegex("[Id, Name, Password can' t blank]");
        }

        public function testPostRegisterValidateDuplicate()
        {
            $user = new User();
            $user->setId("akm");
            $user->setName("Akmal Muhammad P");
            $user->setPassword("12345678");

            $this->userRepository->save($user);

            $_POST['id'] = 'akm';
            $_POST['name'] = 'Akmal Muhammad P';
            $_POST['password'] = '12345678';

            $this->userController->postRegister();

            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Register New User]');
            $this->expectOutputRegex("[User id is already exist]");
        }

        public function testLogin()
        {
            $this->userController->login();

            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Sign On]");
        }

        public function testPostLoginSuccess()
        {
            $user = new User();
            $user->setId('akm');
            $user->setName('Akmal Muhammad P');
            $user->setPassword(password_hash('123456789', PASSWORD_BCRYPT));

            $this->userRepository->save($user);

            $_POST['id'] = 'akm';
            $_POST['password'] = '123456789';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[X-AKM-SESSION]");
        }

        public function testPostLoginValidateError()
        {
            $_POST['id'] = '';
            $_POST['password'] = null;

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Id, Password can' t blank]");
        }

        public function testPostLoginWrongPassword()
        {
            $user = new User();
            $user->setId('akm');
            $user->setName('Akmal Muhammad P');
            $user->setPassword(password_hash('123456789', PASSWORD_BCRYPT));

            $this->userRepository->save($user);

            $_POST['id'] = 'akm';
            $_POST['password'] = 'salah21345';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Id or Password is wrong]");
        }

        public function testLogout()
        {
            $user = new User();
            $user->setId("akm");
            $user->setName("Akmal Muhammad P");
            $user->setPassword(password_hash("sahabatku123", PASSWORD_BCRYPT));
            $this->userRepository->save($user);

            $session = new Session();
            $session->setId(uniqid());
            $session->setUserId($user->getId());
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::getCOOKIENAME()] = $session->getId();

            $this->userController->logout();

            $this->expectOutputRegex("Location: /");
            $this->expectOutputRegex("[X-AKM-SESSION]");
        }

        public function testUpdateProfile()
        {
            $user = new User();
            $user->setId("akm");
            $user->setName("Akmal Muhammad P");
            $user->setPassword(password_hash("sahabatku123", PASSWORD_BCRYPT));
            $this->userRepository->save($user);

            $session = new Session();
            $session->setId(uniqid());
            $session->setUserId($user->getId());
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::getCOOKIENAME()] = $session->getId();

            $this->userController->updateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[akm]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Akmal Muhammad P]");
        }

        public function testPostUpdateProfileSuccess()
        {
            $user = new User();
            $user->setId("akm");
            $user->setName("Akmal Muhammad P");
            $user->setPassword(password_hash("sahabatku123", PASSWORD_BCRYPT));
            $this->userRepository->save($user);

            $session = new Session();
            $session->setId(uniqid());
            $session->setUserId($user->getId());
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::getCOOKIENAME()] = $session->getId();

            $_POST['name'] = 'budi';
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findByID($user->getId());

            self::assertEquals("budi", $result->getName());
        }

        public function testPostUpdateProfileValidationError()
        {
            $user = new User();
            $user->setId("akm");
            $user->setName("Akmal Muhammad P");
            $user->setPassword(password_hash("sahabatku123", PASSWORD_BCRYPT));
            $this->userRepository->save($user);

            $session = new Session();
            $session->setId(uniqid());
            $session->setUserId($user->getId());
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::getCOOKIENAME()] = $session->getId();

            $_POST['name'] = '';
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[akm]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Id, Name can' t blank]");
        }

        public function testUpdatePasswordSuccess()
        {
            $user = new User();
            $user->setId("akm");
            $user->setName("Akmal Muhammad P");
            $user->setPassword(password_hash("sahabatku123", PASSWORD_BCRYPT));
            $this->userRepository->save($user);

            $session = new Session();
            $session->setId(uniqid());
            $session->setUserId($user->getId());
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::getCOOKIENAME()] = $session->getId();

            $this->userController->updatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[akm]");
        }

        public function testPostUpdatePasswordSuccess()
        {
            $user = new User();
            $user->setId("akm");
            $user->setName("Akmal Muhammad P");
            $user->setPassword(password_hash("sahabatku", PASSWORD_BCRYPT));
            $this->userRepository->save($user);

            $session = new Session();
            $session->setId(uniqid());
            $session->setUserId($user->getId());
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::getCOOKIENAME()] = $session->getId();

            $_POST['oldPassword'] = "sahabatku";
            $_POST['newPassword'] = "eekmu241";

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findByID($user->getId());
            self::assertTrue(password_verify($_POST['newPassword'], $result->getPassword()));
        }

        public function testUpdatePasswordValidationError()
        {
            $user = new User();
            $user->setId("akm");
            $user->setName("Akmal Muhammad P");
            $user->setPassword(password_hash("sahabatku", PASSWORD_BCRYPT));
            $this->userRepository->save($user);

            $session = new Session();
            $session->setId(uniqid());
            $session->setUserId($user->getId());
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::getCOOKIENAME()] = $session->getId();

            $_POST['oldPassword'] = "sahabatku";
            $_POST['newPassword'] = "";

            $this->userController->postUpdatePassword();
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Id, Old Password, New Password can' t blank]");
        }

        public function testUpdatePasswordWrongOldPassword()
        {
            $user = new User();
            $user->setId("akm");
            $user->setName("Akmal Muhammad P");
            $user->setPassword(password_hash("sahabatku", PASSWORD_BCRYPT));
            $this->userRepository->save($user);

            $session = new Session();
            $session->setId(uniqid());
            $session->setUserId($user->getId());
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::getCOOKIENAME()] = $session->getId();

            $_POST['oldPassword'] = "sahabatku234";
            $_POST['newPassword'] = "salah123456";

            $this->userController->postUpdatePassword();
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Old password id wrong]");
        }


    }
}








