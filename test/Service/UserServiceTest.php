<?php

namespace Akmalmp\BelajarPhpMvc\Service;

use Akmalmp\BelajarPhpMvc\Config\Database;
use Akmalmp\BelajarPhpMvc\Domain\User;
use Akmalmp\BelajarPhpMvc\Exception\ValidationException;
use Akmalmp\BelajarPhpMvc\Model\UserLoginRequest;
use Akmalmp\BelajarPhpMvc\Model\UserPasswordUpdateRequest;
use Akmalmp\BelajarPhpMvc\Model\UserProfileUpdateRequest;
use Akmalmp\BelajarPhpMvc\Model\UserRegisterRequest;
use Akmalmp\BelajarPhpMvc\Repository\SessionRepository;
use Akmalmp\BelajarPhpMvc\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{

    private UserService $userService;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp():void
    {
        $connection = Database::getConnection();

        $this->sessionRepository = new SessionRepository($connection);
        $this->sessionRepository->deleteAll();

        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);
        $this->userRepository->deleteAll();
    }

    public function testRegisterSuccess()
    {
        $request = new UserRegisterRequest();
        $request->setId("akmal");
        $request->setName("Akmal Muhammad P");
        $request->setPassword("12345678");

        $response = $this->userService->register($request);

        self::assertEquals($request->getId(), $response->getUser()->getId());
        self::assertEquals($request->getName(), $response->getUser()->getName());
        self::assertNotEquals($request->getPassword(), $response->getUser()->getPassword());

        self::assertTrue(password_verify($request->getPassword(), $response->getUser()->getPassword()));
    }

    public function testRegisterFailed()
    {
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->setId("akmal");
        $request->setName("");
        $request->setPassword(null);

        $response = $this->userService->register($request);
    }

    public function testRegisterDuplicate()
    {
        $user = new User();
        $user->setId("akmal");
        $user->setName("Akmal Muhammad P");
        $user->setPassword("12345678");

        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("User id is already exist");

        $request = new UserRegisterRequest();
        $request->setId("akmal");
        $request->setName("Akmal Muhammad P");
        $request->setPassword("12345678");

        $response = $this->userService->register($request);
    }

    public function testRegisterPasswordMoreThan8()
    {
        $this->expectException(ValidationException::class);
        $request = new UserRegisterRequest();
        $request->setId("akmal");
        $request->setName("Akmal Muhammad P");
        $request->setPassword("12345");

        $response = $this->userService->register($request);
    }

    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("Id or Password is wrong");

        $request = new UserLoginRequest();
        $request->setId("akm");
        $request->setPassword("12345678");

        $this->userService->login($request);
    }

    public function testLoginWrongPassword()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("Id or Password is wrong");

        $user = new User();
        $user->setId("akm");
        $user->setName("Akmal Muhammad P");
        $user->setPassword(password_hash("12345678", PASSWORD_BCRYPT));

        $this->userRepository->save($user);

        $request = new UserLoginRequest();
        $request->setId("akm");
        $request->setPassword("123456789");

        $this->userService->login($request);
    }

    public function testLoginSuccess()
    {
        $user = new User();
        $user->setId("akm");
        $user->setName("Akmal Muhammad P");
        $user->setPassword(password_hash("12345678", PASSWORD_BCRYPT));

        $this->userRepository->save($user);

        $request = new UserLoginRequest();
        $request->setId("akm");
        $request->setPassword("12345678");

        $response = $this->userService->login($request);

        self::assertEquals($request->getId(), $response->getUser()->getId());
        self::assertTrue(password_verify($request->getPassword(), $response->getUser()->getPassword()));
    }

    public function testUpdateSuccess()
    {
        $user = new User();
        $user->setId("akm");
        $user->setName("Akmal");
        $user->setPassword(password_hash("sahabatku", PASSWORD_BCRYPT));
        $this->userRepository->save($user);

        $request = new UserProfileUpdateRequest();
        $request->setId("akm");
        $request->setName("Akmla");

        $this->userService->updateProfile($request);

        $user = $this->userRepository->findByID($user->getId());

        $this->assertEquals($request->getName(), $user->getName());
    }

    public function testUpdateValidateError()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("Id, Name can' t blank");

        $request = new UserProfileUpdateRequest();
        $request->setId("");
        $request->setName("");

        $this->userService->updateProfile($request);
    }

    public function testUpdateNotFound()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("User is not found");

        $request = new UserProfileUpdateRequest();
        $request->setId("akm");
        $request->setName("Akmla");

        $this->userService->updateProfile($request);
    }

    public function testUpdatePasswordSuccess()
    {
        $user = new User();
        $user->setId("akm");
        $user->setName("Akmal");
        $user->setPassword(password_hash("sahabatku", PASSWORD_BCRYPT));
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->setId("akm");
        $request->setOldPassword("sahabatku");
        $request->setNewPassword("eekmu241");

        $this->userService->updatePassword($request);
        $result = $this->userRepository->findByID($user->getId());
        self::assertTrue(password_verify($request->getNewPassword(), $result->getPassword()));
    }

    public function testUpdatePasswordValidationError()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("Id, Old Password, New Password can' t blank");

        $request = new UserPasswordUpdateRequest();
        $request->setId("akm");
        $request->setOldPassword("");
        $request->setNewPassword("");

        $this->userService->updatePassword($request);
    }

    public function testUpdatePasswordOldPasswordWrong()
    {
        $user = new User();
        $user->setId("akm");
        $user->setName("Akmal");
        $user->setPassword(password_hash("sahabatku", PASSWORD_BCRYPT));
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->setId("akm");
        $request->setOldPassword("sahabatk444");
        $request->setNewPassword("eekmu241");

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("Old password id wrong");

        $this->userService->updatePassword($request);
    }

    public function testUpdatePasswordNotFound()
    {
        $user = new User();
        $user->setId("akm");
        $user->setName("Akmal");
        $user->setPassword(password_hash("sahabatku", PASSWORD_BCRYPT));
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->setId("akmp");
        $request->setOldPassword("sahabatku");
        $request->setNewPassword("eekmu241");

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("User Id not found");

        $this->userService->updatePassword($request);
    }


}   
