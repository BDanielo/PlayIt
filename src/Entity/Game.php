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
    // 0 waiting for approval
    // 1 approved
    // 2 rejected
    // 3 deletion requested

    #[ORM\ManyToMany(targetEntity: WishList::class, mappedBy: 'games')]
    private Collection $wishLists;




    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->owners = new ArrayCollection();
        $this->authors = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->orderLines = new ArrayCollection();
        $this->wishLists = new ArrayCollection();
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

    // get all owners of the game
    /**
     * @return Collection<int, User>
     */
    public function getOwners(): Collection
    {
        return $this->owners;
    }

    // add a owner to the list
    public function addOwner(User $owner): self
    {
        if (!$this->owners->contains($owner)) {
            $this->owners->add($owner);
        }

        return $this;
    }

    // remove a owner from the list
    public function removeOwner(User $owner): self
    {
        $this->owners->removeElement($owner);

        return $this;
    }

    // get all authors of the game
    /**
     * @return Collection<int, User>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    // add an author to the list
    public function addAuthor(User $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
            $author->addGamesPublished($this);
        }

        return $this;
    }

    // check if author is already in the list
    public function isAuthor(User $author): bool
    {
        return $this->authors->contains($author);
    }

    // remove an author from the list
    public function removeAuthor(User $author): self
    {
        if ($this->authors->removeElement($author)) {
            $author->removeGamesPublished($this);
        }

        return $this;
    }

    // get the number of copy solded
    public function getSold(): ?int
    {
        return $this->sold;
    }

    // set the sold attribute
    public function setSold(int $sold): self
    {
        $this->sold = $sold;

        return $this;
    }

    // add 1 to the sold attribute
    public function addSold(): self
    {
        $this->sold++;
        return $this;
    }

    // get the promotion price of the game if the promotion is valid, if not it just return null
    public function getPromotionPrice(): ?float
    {
        if ($this->promotion !== null && $this->promotionStart !== null && $this->promotionEnd !== null) {
            if ($this->promotionStart <= new \DateTime() && $this->promotionEnd >= new \DateTime()) {
                return $this->price - ($this->price * $this->promotion / 100);
            }
        }
        return null;
    }

    // check if the game is on promotion
    public function hasPromotion(): bool
    {
        if ($this->promotion !== null && $this->promotionStart !== null && $this->promotionEnd !== null) {
            if ($this->promotionStart <= new \DateTime() && $this->promotionEnd >= new \DateTime()) {
                return true;
            }
        }
        return false;
    }

    // get the price of the game
    public function getPrice(): ?float
    {
        return $this->price;
    }

    // set the price of the game
    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    //get the creation date
    public function getVersion(): ?string
    {
        return $this->version;
    }

    //set the version of the game
    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    //get the picture path
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    //* set the picture path [image name are standardized header.jpg, banner.jpg... Only the directory name change for each game]
    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    // TODO implement file upload
    // get the file of the game )
    public function getFile(): ?string
    {
        return $this->file;
    }

    // set the file name
    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    // get the creation date
    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    // set the creation date
    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    // get the last update date
    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updateDate;
    }

    // set last update date
    public function setUpdateDate(?\DateTimeInterface $updateDate): self
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    // get all the reviews of the game
    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    // add review
    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setGames($this);
        }

        return $this;
    }

    // remove review
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

    // convert the entity into a string
    public function __toString(): string
    {
        return $this->name;
    }

    // get all the order lines of the game
    /**
     * @return Collection<int, OrderLine>
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    // add order line
    public function addOrderLine(OrderLine $orderLine): self
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines->add($orderLine);
            $orderLine->setGame($this);
        }

        return $this;
    }

    // remove order line
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

    // get the promotion of the game
    public function getPromotion(): ?float
    {
        return $this->promotion;
    }

    // set the promotion of the game
    public function setPromotion(float $promotion): self
    {
        $this->promotion = $promotion;

        return $this;
    }

    // get the start date of promotion of the game
    public function getPromotionStart(): ?\DateTimeInterface
    {
        return $this->promotionStart;
    }

    // set the start date of promotion of the game
    public function setPromotionStart(?\DateTimeInterface $promotionStart): self
    {
        $this->promotionStart = $promotionStart;

        return $this;
    }

    // get the end date of promotion of the game
    public function getPromotionEnd(): ?\DateTimeInterface
    {
        return $this->promotionEnd;
    }

    // set the end date of promotion of the game
    public function setPromotionEnd(\DateTimeInterface $promotionEnd): self
    {
        $this->promotionEnd = $promotionEnd;

        return $this;
    }

    // delete the promotion of the game
    public function deletePromotion(): self
    {
        $this->promotion = null;
        $this->promotionStart = null;
        $this->promotionEnd = null;

        return $this;
    }

    // get the status of the game
    public function getStatus(): ?int
    {
        return $this->status;
    }

    // set the status of the game
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    // get the status of the game as a string
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

    // get the all the wishlists that contains the game
    /**
     * @return Collection<int, WishList>
     */
    public function getWishLists(): Collection
    {
        return $this->wishLists;
    }

    // add a wishList to the game
    public function addWishList(WishList $wishList): self
    {
        if (!$this->wishLists->contains($wishList)) {
            $this->wishLists->add($wishList);
            $wishList->addGame($this);
        }

        return $this;
    }

    // remove a wishList from the game
    public function removeWishList(WishList $wishList): self
    {
        if ($this->wishLists->removeElement($wishList)) {
            $wishList->removeGame($this);
        }

        return $this;
    }

    // get number of wishes from the game
    public function getWishCount(): int
    {
        return $this->wishLists->count();
    }
}
