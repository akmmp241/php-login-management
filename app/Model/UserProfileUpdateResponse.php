<?php

namespace Akmalmp\BelajarPhpMvc\Model;

use Akmalmp\BelajarPhpMvc\Domain\User;

class UserProfileUpdateResponse
{
    private User $user;

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }


}