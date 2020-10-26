<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Articles\LoadController;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @ApiResource(
 *  normalizationContext={"groups"={"read"}},
 *  itemOperations={"get"},
 *  collectionOperations={
 *    "get",
 *    "load"={
 *     "defaults"={"_api_receive": false},
 *     "method"="POST",
 *     "path"="/articles/load",
 *     "controller"=LoadController::class,
 *     "openapi_context"={
 *      "summary"="Load articles",
 *      "parameters"={
 *		 {
 *		  "in"="body",
 *		  "description"="List of articles",
 *		  "schema"={
 *         "type"="object",
 *         "properties"={
 *          "inventory"={
 *           "type"="array",
 *           "description"="List of articles",
 *           "items"={
 *            "type"="object",
 *            "properties"={
 *             "art_id"={
 *              "type"="string",
 *              "example"="1", 
 *             },
 *             "name"={
 *              "type"="string",
 *              "example"="leg", 
 *             },
 *             "stock"={
 *              "type"="string",
 *              "example"="12", 
 *             },
 *            },
 *           },
 *          },
 *         },
 *       },
 *      },
 *     },
 *    },
 *   },
 *  }
 * )
 */
class Article
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
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read"})
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private $stock;

    /**
     * @ORM\OneToMany(targetEntity=ProductArticle::class, mappedBy="article", orphanRemoval=true, fetch="EAGER")
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
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

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

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
            $productArticle->setArticle($this);
        }

        return $this;
    }

    public function removeProductArticle(ProductArticle $productArticle): self
    {
        if ($this->productArticles->removeElement($productArticle)) {
            // set the owning side to null (unless already changed)
            if ($productArticle->getArticle() === $this) {
                $productArticle->setArticle(null);
            }
        }

        return $this;
    }
}
