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

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Article::class)]
    private Collection $articles;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Comment::class)]
    private Collection $articleComments;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Topic::class)]
    private Collection $userTopics;

    #[ORM\ManyToMany(targetEntity: Topic::class, mappedBy: 'members')]
    private Collection $relatedTopics;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: CommentForTopics::class)]
    private Collection $topicComments;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->articleComments = new ArrayCollection();
        $this->userTopics = new ArrayCollection();
        $this->relatedTopics = new ArrayCollection();
        $this->topicComments = new ArrayCollection();
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
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setAuthor($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getAuthor() === $this) {
                $article->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getArticleComments(): Collection
    {
        return $this->articleComments;
    }

    public function addArticleComment(Comment $articleComment): self
    {
        if (!$this->articleComments->contains($articleComment)) {
            $this->articleComments->add($articleComment);
            $articleComment->setOwner($this);
        }

        return $this;
    }

    public function removeArticleComment(Comment $articleComment): self
    {
        if ($this->articleComments->removeElement($articleComment)) {
            // set the owning side to null (unless already changed)
            if ($articleComment->getOwner() === $this) {
                $articleComment->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Topic>
     */
    public function getUserTopics(): Collection
    {
        return $this->userTopics;
    }

    public function addUserTopic(Topic $userTopic): self
    {
        if (!$this->userTopics->contains($userTopic)) {
            $this->userTopics->add($userTopic);
            $userTopic->setAuthor($this);
        }

        return $this;
    }

    public function removeUserTopic(Topic $userTopic): self
    {
        if ($this->userTopics->removeElement($userTopic)) {
            // set the owning side to null (unless already changed)
            if ($userTopic->getAuthor() === $this) {
                $userTopic->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Topic>
     */
    public function getRelatedTopics(): Collection
    {
        return $this->relatedTopics;
    }

    public function addRelatedTopic(Topic $relatedTopic): self
    {
        if (!$this->relatedTopics->contains($relatedTopic)) {
            $this->relatedTopics->add($relatedTopic);
            $relatedTopic->addMember($this);
        }

        return $this;
    }

    public function removeRelatedTopic(Topic $relatedTopic): self
    {
        if ($this->relatedTopics->removeElement($relatedTopic)) {
            $relatedTopic->removeMember($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, CommentForTopics>
     */
    public function getTopicComments(): Collection
    {
        return $this->topicComments;
    }

    public function addTopicComment(CommentForTopics $topicComment): self
    {
        if (!$this->topicComments->contains($topicComment)) {
            $this->topicComments->add($topicComment);
            $topicComment->setOwner($this);
        }

        return $this;
    }

    public function removeTopicComment(CommentForTopics $topicComment): self
    {
        if ($this->topicComments->removeElement($topicComment)) {
            // set the owning side to null (unless already changed)
            if ($topicComment->getOwner() === $this) {
                $topicComment->setOwner(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return "@" . $this->username;
    }

}
