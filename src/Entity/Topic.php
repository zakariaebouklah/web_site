<?php

namespace App\Entity;

use App\Repository\TopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: TopicRepository::class)]
class Topic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $project = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'topics')]
    private Collection $members;

    /**
     * @Gedmo\Slug(fields={"title"})
     */
    #[ORM\Column(type: Types::TEXT)]
    private string $slug;

    #[ORM\OneToMany(mappedBy: 'topic', targetEntity: CommentForTopics::class)]
    private Collection $commentForTopics;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->commentForTopics = new ArrayCollection();
    }

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

    public function getProject(): ?string
    {
        return $this->project;
    }

    public function setProject(string $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    /**
     * @return Collection<int, User>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        $this->members->removeElement($member);

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
     * @return Collection<int, CommentForTopics>
     */
    public function getCommentForTopics(): Collection
    {
        return $this->commentForTopics;
    }

    public function addCommentForTopic(CommentForTopics $commentForTopic): self
    {
        if (!$this->commentForTopics->contains($commentForTopic)) {
            $this->commentForTopics->add($commentForTopic);
            $commentForTopic->setTopic($this);
        }

        return $this;
    }

    public function removeCommentForTopic(CommentForTopics $commentForTopic): self
    {
        if ($this->commentForTopics->removeElement($commentForTopic)) {
            // set the owning side to null (unless already changed)
            if ($commentForTopic->getTopic() === $this) {
                $commentForTopic->setTopic(null);
            }
        }

        return $this;
    }

}
