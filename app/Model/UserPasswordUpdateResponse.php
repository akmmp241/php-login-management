<?php

namespace Akmalmp\BelajarPhpMvc\Model;

use Akmalmp\BelajarPhpMvc\Domain\User;

class UserPasswordUpdateResponse
{
    private User $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }


}