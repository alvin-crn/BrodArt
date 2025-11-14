<?php

namespace App\Entity;

use App\Repository\SizeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SizeRepository::class)]
class Size
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $size;

    #[ORM\OneToMany(mappedBy: 'size', targetEntity: ProductSize::class)]
    private $productSizes;

    public function __construct()
    {
        $this->productSizes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getSize();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

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
            $productSize->setSize($this);
        }

        return $this;
    }

    public function removeProductSize(ProductSize $productSize): self
    {
        if ($this->productSizes->removeElement($productSize)) {
            // set the owning side to null (unless already changed)
            if ($productSize->getSize() === $this) {
                $productSize->setSize(null);
            }
        }

        return $this;
    }
}
