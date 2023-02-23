<?php

namespace Akmalmp\BelajarPhpMvc\Service;

use Akmalmp\BelajarPhpMvc\Config\Database;
use Akmalmp\BelajarPhpMvc\Domain\Session;
use Akmalmp\BelajarPhpMvc\Domain\User;
use Akmalmp\BelajarPhpMvc\Repository\SessionRepository;
use Akmalmp\BelajarPhpMvc\Repository\UserRepository;
use Exception;

class SessionService
{
    private static string $COOKIE_NAME = "X-AKM-SESSION";
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    public function __construct(SessionRepository $sessionRepository, UserRepository $userRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->userRepository = $userRepository;
    }

    public static function getCOOKIENAME(): string
    {
        return self::$COOKIE_NAME;
    }

    /**
     * @throws Exception
     */
    public function create(string $userId): Session
    {
//        create session
        $session = new Session();
        $session->setId(uniqid());
        $session->setUserId($userId);

        try {
//            query session to database
            Database::getConnection()->beginTransaction();
            $this->sessionRepository->save($session);
            Database::getConnection()->commit();
        } catch (Exception $exception) {
            Database::getConnection()->rollBack();
            throw $exception;
        }

//        set cookie name from session id
        setcookie(self::$COOKIE_NAME, $session->getId(), time() + (60 * 60 * 24), "/");

        return $session;
    }

    public function destroy(): void
    {
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? '';
        $this->sessionRepository->deleteById($sessionId);

        setcookie(self::$COOKIE_NAME, '', 1, "/");
    }

    public function current(): ?User
    {
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? '';
        $session = $this->sessionRepository->findById($sessionId);
        if ($session == null) {
            return null;
        }

        return $this->userRepository->findByID($session->getUserId());
    }
}



