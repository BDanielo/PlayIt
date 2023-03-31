<?php

namespace App\DTO;

use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePromotionDTO
{

    #[Assert\Length(max: 3)]
    public ?string $promotion = '0';

    #[Assert\NotBlank]
    #[Assert\GreaterThan('today')]
    public ?DateTime $promotionStart = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThan('today')]
    public ?DateTime $promotionEnd = null;
}
