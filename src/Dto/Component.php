<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class Component implements DtoInterface
{
    private string $code;
    private string $amount;

    /**
     * @Assert\Length(
     *  min=1,
     *  allowEmptyString = false
     * )
     */
    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @Assert\GreaterThanOrEqual(1)
     */
    public function getAmount(): int
    {
        return (int) $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
