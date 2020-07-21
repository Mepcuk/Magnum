<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     collectionOperations={"get", "post"},
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"product:read", "product:item:get"}},
 *          },
 *          "put",
 *          "delete"
 *     },
 *     shortName="Products",
 *     normalizationContext={"groups"={"product:read"}, "swagger_definition_name"="Read"},
 *     denormalizationContext={"groups"={"product:write"}, "swagger_definition_name"="Write"},
 *     attributes={
 *          "pagination_items_per_page"=10,
 *          "formats"={"jsonld", "json", "html", "jsonhal", "csv"={"text/csv"}}
 *     }
 * )
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ApiFilter(SearchFilter::class, properties={"title": "partial"})
 * @ApiFilter(RangeFilter::class, properties={"priceWoVAT"})
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"product:read", "product:write"})
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=3,
     *     max=255
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"product:read", "product:write"})
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=3,
     *     max=255
     * )
     */
    private $positionNumber;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"product:read", "product:write"})
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=3,
     *     max=255
     * )
     */
    private $productCode;

    /**
     * @ORM\Column(type="float")
     * @Groups({"product:read", "product:write"})
     * @Assert\NotBlank()
     */
    private $priceWoVAT;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"product:read", "product:write"})
     * @Assert\NotBlank()
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Catalog", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"product:read", "product:write"})
     */
    private $catalog;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPositionNumber(): ?string
    {
        return $this->positionNumber;
    }

    public function setPositionNumber(string $positionNumber): self
    {
        $this->positionNumber = $positionNumber;

        return $this;
    }

    public function getProductCode(): ?string
    {
        return $this->productCode;
    }

    public function setProductCode(string $productCode): self
    {
        $this->productCode = $productCode;

        return $this;
    }

    public function getPriceWoVAT(): ?float
    {
        return $this->priceWoVAT;
    }

    public function setPriceWoVAT(float $priceWoVAT): self
    {
        $this->priceWoVAT = $priceWoVAT;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCatalog(): ?Catalog
    {
        return $this->catalog;
    }

    public function setCatalog(?Catalog $catalog): self
    {
        $this->catalog = $catalog;

        return $this;
    }
}
