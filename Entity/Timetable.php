<?php

namespace Disjfa\TimetableBundle\Entity;

use Disjfa\UserBundle\Contracts\UserEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Table(name: 'timetable')]
#[ORM\Entity]
class Timetable
{
    /**
     * @var string
     */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\Column(name: 'title', type: 'string')]
    private string $title;

    #[ORM\Column(name: 'about', type: 'text', nullable: true)]
    private ?string $about;

    #[ORM\Column(name: 'side', type: 'string')]
    private string $side;

    /**
     * @var TimetablePlace[]|ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: TimetablePlace::class, mappedBy: 'timetable')]
    #[ORM\OrderBy(['seqnr' => 'ASC'])]
    private $places;

    /**
     * @var TimetableDate[]|ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: TimetableDate::class, mappedBy: 'timetable')]
    #[ORM\OrderBy(['dateAt' => 'ASC'])]
    private $dates;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'body_bg', type: 'string', nullable: true)]
    private $bodyBg;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'header_bg', type: 'string', nullable: true)]
    private $headerBg;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'box_bg', type: 'string', nullable: true)]
    private $boxBg;

    #[ORM\ManyToMany(targetEntity: UserEntityInterface::class)]
    #[ORM\JoinTable(name: 'timetable_user')]
    private $users;

    public function __construct()
    {
        $this->side = 'horizontal';
        $this->users = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function getAbout(): string
    {
        return (string) $this->about;
    }

    public function setAbout(?string $about): void
    {
        $this->about = $about;
    }

    /**
     * @return TimetablePlace[]|ArrayCollection
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * @return TimetableDate[]|ArrayCollection
     */
    public function getDates()
    {
        return $this->dates;
    }

    public function getSide(): string
    {
        return $this->side;
    }

    public function setSide(string $side): void
    {
        $this->side = $side;
    }

    public function getBodyBg(): ?string
    {
        return $this->bodyBg;
    }

    public function setBodyBg(?string $bodyBg): void
    {
        $this->bodyBg = $bodyBg;
    }

    public function getHeaderBg(): ?string
    {
        return $this->headerBg;
    }

    public function setHeaderBg(?string $headerBg): void
    {
        $this->headerBg = $headerBg;
    }

    public function getBoxBg(): ?string
    {
        return $this->boxBg;
    }

    public function setBoxBg(?string $boxBg): void
    {
        $this->boxBg = $boxBg;
    }

    /** @return Collection<int, UserEntityInterface> */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(UserEntityInterface $user): void
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }
    }

    public function removeUser(UserEntityInterface $user): void
    {
        $this->users->removeElement($user);
    }
}
