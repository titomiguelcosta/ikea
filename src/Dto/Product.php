<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class Product implements DtoInterface
{
    private string $name;
    private float $price = 9.99;

    /**
     * @var Component[]
     */
    private $components = [];

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

    /**
     * @Assert\GreaterThanOrEqual(0.01)
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Component[]
     * @Assert\Valid()
     * @Assert\All({
     *  @Assert\Type(Component::class)
     * })
     * @Assert\Count(
     *  min = 1,
     *  minMessage = "At least one component"
     * )
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    /**
     * @param $components Component[]
     */
    public function setComponents(array $components): self
    {
        $this->components = $components;

        return $this;
    }
}
