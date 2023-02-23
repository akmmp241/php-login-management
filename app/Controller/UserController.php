<?php

namespace Akmalmp\BelajarPhpMvc\Controller;

use Akmalmp\BelajarPhpMvc\App\View;
use Akmalmp\BelajarPhpMvc\Config\Database;
use Akmalmp\BelajarPhpMvc\Exception\ValidationException;
use Akmalmp\BelajarPhpMvc\Model\UserLoginRequest;
use Akmalmp\BelajarPhpMvc\Model\UserPasswordUpdateRequest;
use Akmalmp\BelajarPhpMvc\Model\UserProfileUpdateRequest;
use Akmalmp\BelajarPhpMvc\Model\UserRegisterRequest;
use Akmalmp\BelajarPhpMvc\Repository\SessionRepository;
use Akmalmp\BelajarPhpMvc\Repository\UserRepository;
use Akmalmp\BelajarPhpMvc\Service\SessionService;
use Akmalmp\BelajarPhpMvc\Service\UserService;
use Exception;

class UserController
{
    private UserService $userService;
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);

        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }


    public function register(): void
    {
//        open register page
        View::render('User/register', [
            'title' => 'Register New User'
        ]);
    }

    public function postRegister(): void
    {
//        make register request from register form
        $request = new UserRegisterRequest();
        $request->setId($_POST['id']);
        $request->setName($_POST['name']);
        $request->setPassword($_POST['password']);

        try {
            $this->userService->register($request);
//            Redirect to login page
            View::redirect('/users/login');
        } catch (ValidationException $exception) {
//            still on register page
            View::render('/User/register', [
                'title' => 'Register',
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function login(): void
    {
//        open login page
        View::render('User/login', [
            'title' => 'Login user'
        ]);
    }

    public function postLogin(): void
    {
//        make login request from login form
        $request = new UserLoginRequest();
        $request->setId($_POST['id']);
        $request->setPassword($_POST['password']);

        try {
            $response = $this->userService->login($request);
//            Create Session
            $this->sessionService->create($response->getUser()->getId());
//            Redirect to home 'dashboard'
            View::redirect('/');
        } catch (ValidationException $exception) {
//            still on login page
            View::render('User/login', [
                'title' => 'Login user',
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function logout(): void
    {
//        destroy session & redirect to home 'index'
        $this->sessionService->destroy();
        View::redirect("/");
    }

    public function updateProfile(): void
    {
        $user = $this->sessionService->current();
//        open profile page
        View::render('User/profile', [
            'title' => 'Update user profile',
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getName()
            ]
        ]);
    }

    public function postUpdateProfile(): void
    {
        $user = $this->sessionService->current();
//        make request from post form
        $request = new UserProfileUpdateRequest();
        $request->setId($user->getId());
        $request->setName($_POST['name']);

        try {
            $this->userService->updateProfile($request);
//            Redirect to home 'dashboard'
            View::redirect('/');
        } catch (Exception $exception) {
            $user = $this->sessionService->current();
//            still on profile page
            View::render('User/profile', [
                'title' => 'Update user profile',
                'error' => $exception->getMessage(),
                'user' => [
                    'id' => $user->getId(),
                    'name' => $_POST['name']
                ]
            ]);
        }
    }

    public function updatePassword(): void
    {
        $user = $this->sessionService->current();
        View::render("User/password", [
            'title' => 'Update password user',
            'user' => [
                'id' => $user->getId()
            ]
        ]);
    }

    public function postUpdatePassword(): void
    {
        $user = $this->sessionService->current();

        $request = new UserPasswordUpdateRequest();
        $request->setId($user->getId());
        $request->setOldPassword($_POST['oldPassword']);
        $request->setNewPassword($_POST['newPassword']);

        try {
            $this->userService->updatePassword($request);
            View::redirect("/");
        } catch (Exception $exception) {
            $user = $this->sessionService->current();
            View::render("User/password", [
                'title' => 'Update password user',
                'error' => $exception->getMessage(),
                'user' => [
                    'id' => $user->getId()
                ]
            ]);
        }
    }
}