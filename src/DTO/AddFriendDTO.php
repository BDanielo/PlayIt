<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AddFriendDTO
{
    // add user field type EntityUser
    #[Assert\NotBlank]
    #[Assert\NotNull]
    public $user;

    // userId
    #[Assert\NotBlank]
    #[Assert\NotNull]
    public $userId;
}
