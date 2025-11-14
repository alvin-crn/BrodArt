<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $slug;

    #[ORM\Column(type: 'string', length: 255)]
    private $illustration;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $illustration2;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $illustration3;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $illustration4;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $illustration5;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $illustration6;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $illustration7;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $illustration8;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $illustration9;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $illustration10;

    #[ORM\Column(type: 'string', length: 255)]
    private $subtitle;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\Column(type: 'float')]
    private $price;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'float', nullable: true)]
    private $promo;

    #[ORM\ManyToOne(targetEntity: Color::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private $color;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductSize::class)]
    private $productSizes;

    #[ORM\Column(type: 'float')]
    private $deliveryCost;

    #[ORM\Column(type: 'boolean')]
    private $isBest;

    public function __construct()
    {
        $this->productSizes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getIllustration(): ?string
    {
        return $this->illustration;
    }

    public function setIllustration(string $illustration): self
    {
        $this->illustration = $illustration;

        return $this;
    }

    public function getIllustration2(): ?string
    {
        return $this->illustration2;
    }

    public function setIllustration2(?string $illustration2): self
    {
        $this->illustration2 = $illustration2;

        return $this;
    }

    public function getIllustration3(): ?string
    {
        return $this->illustration3;
    }

    public function setIllustration3(?string $illustration3): self
    {
        $this->illustration3 = $illustration3;

        return $this;
    }

    public function getIllustration4(): ?string
    {
        return $this->illustration4;
    }

    public function setIllustration4(?string $illustration4): self
    {
        $this->illustration4 = $illustration4;

        return $this;
    }

    public function getIllustration5(): ?string
    {
        return $this->illustration5;
    }

    public function setIllustration5(?string $illustration5): self
    {
        $this->illustration5 = $illustration5;

        return $this;
    }

    public function getIllustration6(): ?string
    {
        return $this->illustration6;
    }

    public function setIllustration6(?string $illustration6): self
    {
        $this->illustration6 = $illustration6;

        return $this;
    }

    public function getIllustration7(): ?string
    {
        return $this->illustration7;
    }

    public function setIllustration7(?string $illustration7): self
    {
        $this->illustration7 = $illustration7;

        return $this;
    }

    public function getIllustration8(): ?string
    {
        return $this->illustration8;
    }

    public function setIllustration8(?string $illustration8): self
    {
        $this->illustration8 = $illustration8;

        return $this;
    }

    public function getIllustration9(): ?string
    {
        return $this->illustration9;
    }

    public function setIllustration9(?string $illustration9): self
    {
        $this->illustration9 = $illustration9;

        return $this;
    }

    public function getIllustration10(): ?string
    {
        return $this->illustration10;
    }

    public function setIllustration10(?string $illustration10): self
    {
        $this->illustration10 = $illustration10;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPromo(): ?float
    {
        return $this->promo;
    }

    public function setPromo(?float $promo): self
    {
        $this->promo = $promo;

        return $this;
    }

    public function getColor(): ?Color
    {
        return $this->color;
    }

    public function setColor(?Color $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection<int, ProductSize>
     */
    public function getProductSizes(): Collection
    {
        return $this->productSizes;
    }

    public function addProductSize(ProductSize $productSize): self
    {
        if (!$this->productSizes->contains($productSize)) {
            $this->productSizes[] = $productSize;
            $productSize->setProduct($this);
        }

        return $this;
    }

    public function removeProductSize(ProductSize $productSize): self
    {
        if ($this->productSizes->removeElement($productSize)) {
            // set the owning side to null (unless already changed)
            if ($productSize->getProduct() === $this) {
                $productSize->setProduct(null);
            }
        }

        return $this;
    }

    public function getDeliveryCost(): ?float
    {
        return $this->deliveryCost;
    }

    public function setDeliveryCost(float $deliveryCost): self
    {
        $this->deliveryCost = $deliveryCost;

        return $this;
    }

    public function getIsBest(): ?bool
    {
        return $this->isBest;
    }

    public function setIsBest(bool $isBest): self
    {
        $this->isBest = $isBest;

        return $this;
    }

}
