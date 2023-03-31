<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AddFriendDTO
{
    // userId
    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    public $userId;

    // add user field which is an USer type
    #[Assert\NotBlank]
    #[Assert\Type('App\Entity\User')]
    public $user;
}
