<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class Article implements DtoInterface
{
    private string $stock;
    private string $code;
    private string $name;

    /**
     * @Assert\GreaterThanOrEqual(1)
     */
    public function getStock(): int
    {
        return (int) $this->stock;
    }

    public function setStock(string $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

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
     * @Assert\Length(
     *  min=1,
     *  allowEmptyString = false
     * )
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
