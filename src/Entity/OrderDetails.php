<?php

namespace App\Entity;

use App\Entity\ProductSize;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\OrderDetailsRepository;

#[ORM\Entity(repositoryClass: OrderDetailsRepository::class)]
class OrderDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private $myOrder;

    #[ORM\Column(type: 'string', length: 255)]
    private $product;

    #[ORM\Column(type: 'integer')]
    private $quantity;

    #[ORM\Column(type: 'float')]
    private $price;

    #[ORM\Column(type: 'float')]
    private $total;

    #[ORM\Column(type: 'string', length: 255)]
    private $size;

    #[ORM\Column(type: 'string', length: 255)]
    private $photoClient;

    #[ORM\ManyToOne(targetEntity: ProductSize::class)]
    #[ORM\JoinColumn(name: 'id_size_stock', referencedColumnName: 'id', nullable: false)]
    private ?ProductSize $productSize = null;

    public function __toString()
    {
        return $this->getQuantity() . ' x ' . $this->getProduct() . ' en taille ' . $this->getSize() . ' avec la photo ' . $this->getPhotoClient() . ' (à ' . number_format(($this->getPrice() / 100), 2, ',', ',') . '€ l\'unité)';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMyOrder(): ?Order
    {
        return $this->myOrder;
    }

    public function setMyOrder(?Order $myOrder): self
    {
        $this->myOrder = $myOrder;

        return $this;
    }

    public function getProduct(): ?string
    {
        return $this->product;
    }

    public function setProduct(string $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

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

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
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

    public function getPhotoClient(): ?string
    {
        return $this->photoClient;
    }

    public function setPhotoClient(string $photoClient): self
    {
        $this->photoClient = $photoClient;

        return $this;
    }

    public function getProductSize(): ?ProductSize
    {
        return $this->productSize;
    }

    public function setProductSize(ProductSize $productSize): self
    {
        $this->productSize = $productSize;
        return $this;
    }
}
