<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\ManyToMany(targetEntity: Game::class, mappedBy: 'owners')]
    private Collection $gamesOwned;

    #[ORM\ManyToMany(targetEntity: Game::class, inversedBy: 'authors')]
    private Collection $gamesPublished;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(type: 'json')]
    private $roles = ['ROLE_USER'];

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column]
    private ?int $points = 0;

    #[ORM\Column]
    private ?int $levels = 0;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $signupDate = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $lastSigninDateTime = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Review::class)]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Order::class)]
    private Collection $orders;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?WishList $wishList = null;
    #[ORM\ManyToMany(targetEntity: Badge::class, mappedBy: 'users')]
    private Collection $badges;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'FriendReceived')]
    private Collection $FriendSended;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'FriendSended')]
    private Collection $FriendReceived;

    #[ORM\OneToMany(mappedBy: 'Sender', targetEntity: ChatMessage::class, orphanRemoval: true)]
    private Collection $chatMessagesSended;

    #[ORM\OneToMany(mappedBy: 'Receiver', targetEntity: ChatMessage::class)]
    private Collection $chatMessagesReceived;

    public function __construct()
    {
        $this->gamesOwned = new ArrayCollection();
        $this->gamesPublished = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->badges = new ArrayCollection();
        $this->FriendSended = new ArrayCollection();
        $this->FriendReceived = new ArrayCollection();
        $this->chatMessagesSended = new ArrayCollection();
        $this->chatMessagesReceived = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     * @return mixed
     */
    public function eraseCredentials()
    {
    }

    /**
     * Returns the identifier for this user (e.g. username or email address).
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFullname(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getLevels(): ?int
    {
        return $this->levels;
    }

    public function setLevels(int $levels): self
    {
        $this->levels = $levels;

        return $this;
    }

    public function getSignupDate(): ?\DateTimeInterface
    {
        return $this->signupDate;
    }

    public function setSignupDate(\DateTimeInterface $signupDate): self
    {
        $this->signupDate = $signupDate;

        return $this;
    }

    public function getLastSigninDateTime(): ?\DateTimeInterface
    {
        return $this->lastSigninDateTime;
    }

    public function setLastSigninDateTime(\DateTimeInterface $lastSigninDateTime): self
    {
        $this->lastSigninDateTime = $lastSigninDateTime;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGamesOwned(): Collection
    {
        return $this->gamesOwned;
    }

    public function addGamesOwned(Game $gamesOwned): self
    {
        if (!$this->gamesOwned->contains($gamesOwned)) {
            $this->gamesOwned->add($gamesOwned);
            $gamesOwned->addOwner($this);
        }

        return $this;
    }

    public function removeGamesOwned(Game $gamesOwned): self
    {
        if ($this->gamesOwned->removeElement($gamesOwned)) {
            $gamesOwned->removeOwner($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGamesPublished(): Collection
    {
        return $this->gamesPublished;
    }

    public function addGamesPublished(Game $gamesPublished): self
    {
        if (!$this->gamesPublished->contains($gamesPublished)) {
            $this->gamesPublished->add($gamesPublished);
        }

        return $this;
    }

    public function removeGamesPublished(Game $gamesPublished): self
    {
        $this->gamesPublished->removeElement($gamesPublished);

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
            $review->setAuthor($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getAuthor() === $this) {
                $review->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    public function getWishList(): ?WishList
    {
        return $this->wishList;
    }

    public function setWishList(WishList $wishList): self
    {
        // set the owning side of the relation if necessary
        if ($wishList->getUser() !== $this) {
            $wishList->setUser($this);
        }

        $this->wishList = $wishList;

        return $this;
    }

    /**
     * @return Collection<int, Badge>
     */

    public function getBadges(): Collection
    {
        return $this->badges;
    }

    public function addBadge(Badge $badge): self
    {
        if (!$this->badges->contains($badge)) {
            $this->badges->add($badge);
            $badge->addUser($this);
        }

        return $this;
    }

    public function removeBadge(Badge $badge): self
    {
        if ($this->badges->removeElement($badge)) {
            $badge->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFriendSended(): Collection
    {
        return $this->FriendSended;
    }

    public function addFriendSended(self $friendSended): self
    {
        if (!$this->FriendSended->contains($friendSended)) {
            $this->FriendSended->add($friendSended);
        }

        return $this;
    }

    public function removeFriendSended(self $friendSended): self
    {
        $this->FriendSended->removeElement($friendSended);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFriendReceived(): Collection
    {
        return $this->FriendReceived;
    }

    public function addFriendReceived(self $friendReceived): self
    {
        if (!$this->FriendReceived->contains($friendReceived)) {
            $this->FriendReceived->add($friendReceived);
            $friendReceived->addFriendSended($this);
        }

        return $this;
    }

    public function removeFriendReceived(self $friendReceived): self
    {
        if ($this->FriendReceived->removeElement($friendReceived)) {
            $friendReceived->removeFriendSended($this);
        }

        return $this;
    }

    public function getFriends(): Collection
    {
        $friends = new ArrayCollection();
        $friendsReceived = $this->getFriendReceived();
        $friendsSended = $this->getFriendSended();

        foreach ($friendsReceived as $friend) {
            $friends->add($friend);
        }

        foreach ($friendsSended as $friend) {
            $friends->add($friend);
        }

        return $friends;
    }

    /**
     * @return Collection<int, ChatMessage>
     */
    public function getChatMessagesSended(): Collection
    {
        return $this->chatMessagesSended;
    }

    public function addChatMessagesSended(ChatMessage $chatMessagesSended): self
    {
        if (!$this->chatMessagesSended->contains($chatMessagesSended)) {
            $this->chatMessagesSended->add($chatMessagesSended);
            $chatMessagesSended->setSender($this);
        }

        return $this;
    }

    public function removeChatMessagesSended(ChatMessage $chatMessagesSended): self
    {
        if ($this->chatMessagesSended->removeElement($chatMessagesSended)) {
            // set the owning side to null (unless already changed)
            if ($chatMessagesSended->getSender() === $this) {
                $chatMessagesSended->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ChatMessage>
     */
    public function getChatMessagesReceived(): Collection
    {
        return $this->chatMessagesReceived;
    }

    public function addChatMessagesReceived(ChatMessage $chatMessagesReceived): self
    {
        if (!$this->chatMessagesReceived->contains($chatMessagesReceived)) {
            $this->chatMessagesReceived->add($chatMessagesReceived);
            $chatMessagesReceived->setReceiver($this);
        }

        return $this;
    }

    public function removeChatMessagesReceived(ChatMessage $chatMessagesReceived): self
    {
        if ($this->chatMessagesReceived->removeElement($chatMessagesReceived)) {
            // set the owning side to null (unless already changed)
            if ($chatMessagesReceived->getReceiver() === $this) {
                $chatMessagesReceived->setReceiver(null);
            }
        }

        return $this;
    }

    public function isGameOwned(Game $game): bool
    {
        return $this->gamesOwned->contains($game);
    }

    // is game is in wishlist
    public function isGameWished(Game $game): bool
    {
        return $this->wishList->getGames()->contains($game);
    }
}
