<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class UserNotActiveException extends AccountStatusException
{
    public function getMessageKey()
    {
        return 'User not active';
    }
}
