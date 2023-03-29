<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'games')]
    private Collection $category;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'gamesOwned')]
    private Collection $owners;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'gamesPublished')]
    private Collection $authors;

    #[ORM\Column]
    private ?int $sold = 0;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 50)]
    private ?string $version = null;

    #[ORM\Column(length: 255)]
    private ?string $picture = null;

    #[ORM\Column(length: 255)]
    private ?string $file = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updateDate = null;

    #[ORM\OneToMany(mappedBy: 'games', targetEntity: Review::class, orphanRemoval: true)]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: OrderLine::class)]
    private Collection $orderLines;

    #[ORM\Column(nullable: true)]
    private ?float $promotion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $promotionStart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $promotionEnd = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $status = 0;
    // 0 wating for approval
    // 1 approved
    // 2 rejected
    // 3 deletion requested
    // 


    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->owners = new ArrayCollection();
        $this->authors = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->orderLines = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->category->removeElement($category);

        return $this;
    }

    public function getCategoryName(): string
    {
        $categoryName = '';
        foreach ($this->category as $category) {
            $categoryName .= $category->getName() . ', ';
        }

        return substr($categoryName, 0, -2);
    }

    /**
     * @return Collection<int, User>
     */
    public function getOwners(): Collection
    {
        return $this->owners;
    }

    public function addOwner(User $owner): self
    {
        if (!$this->owners->contains($owner)) {
            $this->owners->add($owner);
        }

        return $this;
    }

    public function removeOwner(User $owner): self
    {
        $this->owners->removeElement($owner);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(User $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
            $author->addGamesPublished($this);
        }

        return $this;
    }

    public function removeAuthor(User $author): self
    {
        if ($this->authors->removeElement($author)) {
            $author->removeGamesPublished($this);
        }

        return $this;
    }

    public function getSold(): ?int
    {
        return $this->sold;
    }

    public function setSold(int $sold): self
    {
        $this->sold = $sold;

        return $this;
    }

    public function addSold(): self
    {
        $this->sold++;
        return $this;
    }

    public function getPromotionPrice(): ?float
    {
        if ($this->promotion !== null && $this->promotionStart !== null && $this->promotionEnd !== null) {
            if ($this->promotionStart <= new \DateTime() && $this->promotionEnd >= new \DateTime()) {
                return $this->price - ($this->price * $this->promotion / 100);
            }
        }
        return null;
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

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updateDate;
    }

    public function setUpdateDate(?\DateTimeInterface $updateDate): self
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setGames($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getGames() === $this) {
                $review->setGames(null);
            }
        }

        return $this;
    }

    // tostring
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<int, OrderLine>
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    public function addOrderLine(OrderLine $orderLine): self
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines->add($orderLine);
            $orderLine->setGame($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderLine $orderLine): self
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getGame() === $this) {
                $orderLine->setGame(null);
            }
        }

        return $this;
    }

    public function getPromotion(): ?float
    {
        return $this->promotion;
    }

    public function setPromotion(float $promotion): self
    {
        $this->promotion = $promotion;

        return $this;
    }

    public function getPromotionStart(): ?\DateTimeInterface
    {
        return $this->promotionStart;
    }

    public function setPromotionStart(?\DateTimeInterface $promotionStart): self
    {
        $this->promotionStart = $promotionStart;

        return $this;
    }

    public function getPromotionEnd(): ?\DateTimeInterface
    {
        return $this->promotionEnd;
    }

    public function setPromotionEnd(\DateTimeInterface $promotionEnd): self
    {
        $this->promotionEnd = $promotionEnd;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatusString(): string
    {
        switch ($this->status) {
            case 0:
                return 'Waiting for approval';
            case 1:
                return 'Approved';
            case 2:
                return 'Rejected';
            case 3:
                return 'Deletion requested';
            default:
                return 'Unknown';
        }
    }
}
