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
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var string[] $roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class)]
    private Collection $articleComments;

    /**
     * @Gedmo\Slug(fields={"username"})
     */
    #[ORM\Column(type: Types::TEXT)]
    private string $slug;

    #[ORM\ManyToMany(targetEntity: Topic::class, mappedBy: 'members')]
    private Collection $topics;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: CommentForTopics::class)]
    private Collection $topicsComments;

    public function __construct()
    {
        $this->articleComments = new ArrayCollection();
        $this->topics = new ArrayCollection();
        $this->topicsComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
     * @return Collection<int, Comment>
     */
    public function getArticleComments(): Collection
    {
        return $this->articleComments;
    }

    public function addArticleComment(Comment $comment): self
    {
        if (!$this->articleComments->contains($comment)) {
            $this->articleComments->add($comment);
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeArticleComment(Comment $comment): self
    {
        if ($this->articleComments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Topic>
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic): self
    {
        if (!$this->topics->contains($topic)) {
            $this->topics->add($topic);
            $topic->addMember($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): self
    {
        if ($this->topics->removeElement($topic)) {
            $topic->removeMember($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return "@" . $this->username;
    }

    /**
     * @return Collection<int, CommentForTopics>
     */
    public function getTopicsComments(): Collection
    {
        return $this->topicsComments;
    }

    public function addTopicsComment(CommentForTopics $commentForTopic): self
    {
        if (!$this->topicsComments->contains($commentForTopic)) {
            $this->topicsComments->add($commentForTopic);
            $commentForTopic->setAuthor($this);
        }

        return $this;
    }

    public function removeTopicsComment(CommentForTopics $commentForTopic): self
    {
        if ($this->topicsComments->removeElement($commentForTopic)) {
            // set the owning side to null (unless already changed)
            if ($commentForTopic->getAuthor() === $this) {
                $commentForTopic->setAuthor(null);
            }
        }

        return $this;
    }

}
