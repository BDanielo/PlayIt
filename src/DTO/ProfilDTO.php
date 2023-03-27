<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProfilDTO
{

    #[Assert\Length(min: 3)]
    public ?string $firstname = null;

    #[Assert\Length(min: 3)]
    public ?string $lastname = null;

    #[Assert\Email]
    public ?string $email = null;

    #[Assert\Length(min: 5)]
    public ?string $address = null;

    #[Assert\Length(min: 3)]
    public ?string $username = null;

    #[Assert\Length(min: 6)]
    public ?string $currentPassword = null;

    public ?string $newPassword = null;

    public ?string $confirmNewPassword = null;
}
