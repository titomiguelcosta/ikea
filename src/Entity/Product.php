<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Products\LoadController;
use App\Controller\Products\SellController;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ApiResource(
 *  normalizationContext={"groups"={"read"}},
 *  itemOperations={
 *   "get",
 *   "sell"={
 *    "defaults"={"_api_receive": false},
 *    "method"="POST",
 *    "path"="/products/{id}/sell",
 *    "controller"=SellController::class,
 *    "openapi_context"={
 *     "summary"="Sell product",
 *     "parameters"={
 *		{
 *		 "name"="id",
 *		 "in"="path",
 *		 "description"="ID of product",
 *		 "required"="true",
 *		 "type"="integer",
 *      },
 *		{
 *		 "in"="body",
 *		 "description"="Empty body",
 *		 "schema"={},
 *      },
 *     },
 *    },
 *   },
 *  },
 *  collectionOperations={
 *   "get",
 *   "load"={
 *    "defaults"={"_api_receive": false},
 *    "method"="POST",
 *    "path"="/products/load",
 *    "controller"=LoadController::class,
 *    "openapi_context"={
 *     "summary"="Load products",
 *     "parameters"={
 *		{
 *		 "in"="body",
 *		 "description"="List of products",
 *		 "schema"={
 *        "type"="object",
 *        "properties"={
 *         "products"={
 *          "type"="array",
 *          "description"="List of products",
 *          "items"={
 *           "type"="object",
 *           "properties"={
 *            "name"={
 *             "type"="string",
 *             "example"="Bedside table", 
 *            },
 *            "contain_articles"={
 *             "type"="array",
 *             "items"={
 *              "type"="object",
 *              "properties"={
 *               "art_id"={
 *                "type"="string",
 *                "example"="1",
 *               },
 *               "amount_of"={
 *                "type"="string",
 *                "example"="8", 
 *               },
 *              },
 *             },
 *            },
 *           },
 *          },
 *         },
 *        },
 *       },
 *      },
 *     },
 *    },
 *   },
 *  },
 * )
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read"})
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     * @Groups({"read"})
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity=ProductArticle::class, mappedBy="product", orphanRemoval=true, fetch="EAGER")
     * @Groups({"read"})
     */
    private $productArticles;

    public function __construct()
    {
        $this->productArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|ProductArticle[]
     */
    public function getProductArticles(): Collection
    {
        return $this->productArticles;
    }

    public function addProductArticle(ProductArticle $productArticle): self
    {
        if (!$this->productArticles->contains($productArticle)) {
            $this->productArticles[] = $productArticle;
            $productArticle->setProduct($this);
        }

        return $this;
    }

    public function removeProductArticle(ProductArticle $productArticle): self
    {
        if ($this->productArticles->removeElement($productArticle)) {
            // set the owning side to null (unless already changed)
            if ($productArticle->getProduct() === $this) {
                $productArticle->setProduct(null);
            }
        }

        return $this;
    }

    public function clearProductArticles(): self
    {
        $this->productArticles = new ArrayCollection();

        return $this;
    }

    /**
     * @Groups({"read"})
     * @SerializedName("stock")
     */
    public function getStock(): int
    {
        $stock = 0;

        /* @var ProductArticle */
        foreach ($this->getProductArticles() as $productArticle) {
            $needed = $productArticle->getAmount();
            $available = $productArticle->getArticle()->getStock();
            $total = floor($available / $needed);

            $stock = $stock > 0 ? min($stock, $total) : $total;
        }

        return $stock;
    }
}
