<?php

namespace Akmalmp\BelajarPhpMvc\Service;

use Akmalmp\BelajarPhpMvc\Config\Database;
use Akmalmp\BelajarPhpMvc\Domain\User;
use Akmalmp\BelajarPhpMvc\Exception\ValidationException;
use Akmalmp\BelajarPhpMvc\Model\UserLoginRequest;
use Akmalmp\BelajarPhpMvc\Model\UserLoginResponse;
use Akmalmp\BelajarPhpMvc\Model\UserPasswordUpdateRequest;
use Akmalmp\BelajarPhpMvc\Model\UserProfileUpdateRequest;
use Akmalmp\BelajarPhpMvc\Model\UserProfileUpdateResponse;
use Akmalmp\BelajarPhpMvc\Model\UserRegisterRequest;
use Akmalmp\BelajarPhpMvc\Model\UserRegisterResponse;
use Akmalmp\BelajarPhpMvc\Repository\UserRepository;
use Exception;

class UserService
{

    private UserRepository $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    /**
     * @throws ValidationException
     */
    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        try {
            Database::beginTransaction();
            $this->validateUserRegisterRequest($request);

            $user = $this->userRepository->findByID($request->getId());
            if ($user != null) {
                throw new ValidationException("User id is already exist");
            }

            $user = new User();
            $user->setId($request->getId());
            $user->setName($request->getName());
            $user->setPassword(password_hash($request->getPassword(), PASSWORD_BCRYPT));

            $this->userRepository->save($user);

            $response = new UserRegisterResponse();
            $response->setUser($user);
            Database::commitTransaction();
            return $response;
        } catch (Exception $exception) {
            Database::rollBackTransaction();
            throw $exception;
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateUserRegisterRequest(UserRegisterRequest $request): void
    {
        if ($request->getId() == null || $request->getName() == null || $request->getPassword() == null ||
            trim($request->getId()) == "" || trim($request->getName()) == "" || trim($request->getPassword()
                == "")) {
            throw new ValidationException("Id, Name, Password can' t blank");
        }

        if (strlen($request->getPassword()) < 8) {
            throw new ValidationException("Password at least must be 8 character");
        }
    }

    /**
     * @throws ValidationException
     */
    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->validateUserLoginRequest($request);

        $user = $this->userRepository->findByID($request->getId());

        if ($user == null) {
            throw new ValidationException("Id or Password is wrong");
        }

        if (password_verify($request->getPassword(), $user->getPassword())) {
            $response = new UserLoginResponse();
            $response->setUser($user);
            return $response;
        } else {
            throw new ValidationException("Id or Password is wrong");
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateUserLoginRequest(UserLoginRequest $request): void
    {
        if ($request->getId() == null || $request->getPassword() == null ||
            trim($request->getId()) == "" || trim($request->getPassword()
                == "")) {
            throw new ValidationException("Id, Password can' t blank");
        }

        if (strlen($request->getPassword()) < 8) {
            throw new ValidationException("Password at least must be 8 character");
        }
    }

    /**
     * @throws ValidationException
     */
    public function updateProfile(UserProfileUpdateRequest $request): UserProfileUpdateResponse
    {
        $this->validateUserProfileUpdateRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findByID($request->getId());
            if ($user == null) {
                throw new ValidationException("User is not found");
            }

            $user->setName($request->getName());
            $this->userRepository->update($user);

            Database::commitTransaction();

            $response = new UserProfileUpdateResponse();
            $response->setUser($user);
            return $response;
        } catch (Exception $exception) {
            Database::rollBackTransaction();
            throw $exception;
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateUserProfileUpdateRequest(UserProfileUpdateRequest $request): void
    {
        if ($request->getId() == null || $request->getName() == null ||
            trim($request->getId()) == "" || trim($request->getName()
                == "")) {
            throw new ValidationException("Id, Name can' t blank");
        }
    }

    /**
     * @throws ValidationException
     */
    public function updatePassword(UserPasswordUpdateRequest $request): UserProfileUpdateResponse
    {
        $this->validateUpdatePasswordRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findByID($request->getId());
            if ($user == null) {
                throw new ValidationException("User Id not found");
            }

            if (!password_verify($request->getOldPassword(), $user->getPassword())) {
                throw new ValidationException("Old password id wrong");
            }

            $user->setPassword(password_hash($request->getNewPassword(), PASSWORD_BCRYPT));
            $this->userRepository->update($user);

            Database::commitTransaction();

            $response = new UserProfileUpdateResponse();
            $response->setUser($user);
            return $response;
        } catch (Exception $exception) {
            Database::rollBackTransaction();
            throw $exception;
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateUpdatePasswordRequest(UserPasswordUpdateRequest $request): void
    {
        if ($request->getId() == null || $request->getOldPassword() == null || $request->getNewPassword() == null ||
            trim($request->getId()) == "" || trim($request->getOldPassword()) == "" || trim($request->getNewPassword()
                == "")) {
            throw new ValidationException("Id, Old Password, New Password can' t blank");
        }

        if (strlen($request->getOldPassword()) < 8 || strlen($request->getNewPassword() < 8)) {
            throw new ValidationException("Password at least must be 8 character");
        }
    }
}