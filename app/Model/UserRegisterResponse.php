<?php

namespace Akmalmp\BelajarPhpMvc\Model;

use Akmalmp\BelajarPhpMvc\Domain\User;

class UserRegisterResponse
{
    private User $user;

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }


}