<?php

namespace App\Entity;

use App\Entity\Topic;
use App\Repository\ThreadRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ThreadRepository::class)
 * @ORM\Table(name="threads")
 */
class Thread
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("DateTimeInterface")
     * @var \DateTime
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Topic", mappedBy="thread")
     * @var Topic[]|Collection
     */
    private $topics;

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

    public function getTopics()
    {
        return $this->topics;
    }

    public function setTopics($topics): self
    {
        $this->topics = $topics;

        return $this;
    }
}
