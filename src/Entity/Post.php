<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
/**
 * Post is an object that represent a post, a post can have many categories and many comments and has to be asseced to exactly one user
 */
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @var int|null|null
     */
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    /**
     * @var string|null|null
     */
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    /**
     * @var string|null|null
     */
    private ?string $description = null;

    #[ORM\Column(length: 512, nullable: true)]
    /**
     * @var string|null|null
     */
    private ?string $content = null;

    #[ORM\Column(length: 50)]
    /**
     * @var string|null|null
     */
    private ?string $slug = null;

    #[ORM\Column]
    /**
     * @var \DateTimeImmutable|null|null
     */
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    /**
     * @var \DateTimeImmutable|null|null
     */
    private ?\DateTimeImmutable $updateAt = null;

    #[ORM\Column]
    /**
     * @var \DateTimeImmutable|null|null
     */
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'posts')]
    /**
     * @var Collection
     */
    private Collection $categories;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class)]
    /**
     * @var Collection
     */
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    /**
     * @var User|null|null
     */
    private ?User $Creator = null;

    #[ORM\Column(nullable: true)]
    /**
     * @var int|null|null
     */
    private ?int $score = null;

    #[ORM\OneToMany(mappedBy: 'Post', targetEntity: Vote::class, orphanRemoval: true)]
    /**
     * @var Collection
     */
    private Collection $votes;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * 
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * 
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     * 
     * @return self
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * 
     * @return self
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeImmutable $createdAt
     * 
     * @return self
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    /**
     * @param \DateTimeImmutable $updateAt
     * 
     * @return self
     */
    public function setUpdateAt(\DateTimeImmutable $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTimeImmutable $publishedAt
     * 
     * @return self
     */
    public function setPublishedAt(\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Category $category
     * 
     * @return self
     */
    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    /**
     * @param Category $category
     * 
     * @return self
     */
    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param Comment $comment
     * 
     * @return self
     */
    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    /**
     * @param Comment $comment
     * 
     * @return self
     */
    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return User|null
     */
    public function getCreator(): ?User
    {
        return $this->Creator;
    }

    /**
     * @param User|null $Creator
     * 
     * @return self
     */
    public function setCreator(?User $Creator): self
    {
        $this->Creator = $Creator;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getScore(): ?int
    {
        return $this->score;
    }

    /**
     * @param int|null $score
     * 
     * @return self
     */
    public function setScore(?int $score): self
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @return Collection<int, Vote>
     */
    /**
     * @return Collection
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    /**
     * @param Vote $vote
     * 
     * @return self
     */
    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
            $vote->setPost($this);
        }

        return $this;
    }

    /**
     * @param Vote $vote
     * 
     * @return self
     */
    public function removeVote(Vote $vote): self
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getPost() === $this) {
                $vote->setPost(null);
            }
        }

        return $this;
    }
}
