<?php

namespace App\Entity;

use App\Repository\TopicRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TopicRepository::class)
 * @ORM\Table(name="topics")
 */
class Topic
{
    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->setDate(new \DateTime);
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="To pole nie może być puste")
     * @Assert\Length(max=255, maxMessage="Nazwa tematu nie może zawierać więcej niż {{ limit }} znaków")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=4096)
     * @Assert\NotBlank(message="To pole nie może być puste")
     * @Assert\Length(max=4096, maxMessage="Treść tematu nie może zawierać więcej niż {{ limit }} znaków")
     */
    private $text;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("DateTimeInterface")
     * @var \DateTime
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @var User
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="topic", cascade={"remove"})
     * @var Post[]|Collection
     */
    private $posts;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Thread")
     * @var Thread
     */
    private $thread;

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

    /**
     * Get the value of date
     *
     * @return  \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @param  \DateTime  $date
     *
     * @return  self
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of user
     *
     * @return  User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the value of user
     *
     * @param  User  $user
     *
     * @return  self
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of posts
     *
     * @return  Post[]|Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Set the value of posts
     *
     * @param  Post[]|Collection  $posts
     *
     * @return  self
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;

        return $this;
    }

    /**
     * Get the value of text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set the value of text
     *
     * @return  self
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get the value of thread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set the value of thread
     *
     * @return  self
     */
    public function setThread($thread)
    {
        $this->thread = $thread;

        return $this;
    }
}
