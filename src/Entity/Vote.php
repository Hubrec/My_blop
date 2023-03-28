<?php

namespace App\Entity;

use App\Repository\VoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
/**
 * Vote is an object that represent a vote on a post, a vote can be valid or not and has to be asseces to exactly one post, 
 * the vote has a main parameter witch is the state, the state can be true or false, true for upvote and false for downvote.
 */
class Vote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @var int|null|null
     */
    private ?int $id = null;

    #[ORM\Column]
    /**
     * @var bool|null|null
     */
    private ?bool $state = null;

    #[ORM\ManyToOne(inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    /**
     * @var Post|null|null
     */
    private ?Post $Post = null;

    #[ORM\ManyToOne(inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    /**
     * @var User|null|null
     */
    private ?User $User = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return bool|null
     */
    public function isState(): ?bool
    {
        return $this->state;
    }

    /**
     * @param bool $state
     * 
     * @return self
     */
    public function setState(bool $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Post|null
     */
    public function getPost(): ?Post
    {
        return $this->Post;
    }

    /**
     * @param Post|null $Post
     * 
     * @return self
     */
    public function setPost(?Post $Post): self
    {
        $this->Post = $Post;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->User;
    }

    /**
     * @param User|null $User
     * 
     * @return self
     */
    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }
}
