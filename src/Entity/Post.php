<?php

namespace App\Entity;

use App\Repository\PostRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @ORM\Table(name="posts")
 */
class Post
{
    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @var User
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Topic")
     * @var Topic
     */
    private $topic;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="post", cascade={"remove"})
     * @var Comment[]|Collection
     */
    private $comments;

    /**
     * @ORM\Column(type="string", length=4096)
     * @Assert\Length(min=3, max=4096)
     */
    private $text;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("DateTimeInterface")
     * @var \DateTime
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTopic()
    {
        return $this->topic;
    }

    public function setTopic($topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get the value of date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of comments
     *
     * @return  Comment
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set the value of comments
     *
     * @param  Comment  $comments
     *
     * @return  self
     */
    public function setComments(Comment $comments)
    {
        $this->comments = $comments;

        return $this;
    }
}
