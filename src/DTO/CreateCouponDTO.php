<?php

namespace App\DTO;

use DateTime;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCouponDTO
{

    #[Assert\Type("integer")]
    #[Assert\Length(max: 3)]
    #[Assert\GreaterThan(0)]
    #[Assert\LessThan(101)]
    #[Assert\NotBlank]
    public ?int $percent = null;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    public ?string $code = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThan('today')]
    public ?DateTime $startDate = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThan('today')]
    public ?DateTime $endDate = null;
}
