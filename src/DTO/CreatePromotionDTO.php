<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreatePromotionDTO
{

    #[Assert\Length(min: 3)]
    public ?string $percent = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThan('today')]
    public ?\DateTimeInterface $date_start = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThan('today')]
    public ?\DateTimeInterface $date_end = null;
}
