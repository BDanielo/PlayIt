<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CardVerificationDTO
{

    #[Assert\NotBlank]
    #[Assert\Length(min: 6, max: 100)]
    public ?string $cardOwner = null;

    #[Assert\NotBlank]
    #[Assert\CardScheme(
        schemes: [Assert\CardScheme::VISA],
        message: 'Your credit card number is invalid.',
    )]
    public ?string $cardNumber = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 3)]
    public ?string $cvv = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 2)]
    public ?string $expirationYear = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 2)]
    public ?string $expirationMonth = null;
}
