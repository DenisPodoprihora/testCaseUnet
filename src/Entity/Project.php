<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project implements EntityInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="project_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $projectId;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private string $title;

    /**
     * Many Users have Many Projects.
     * @ORM\ManyToMany(targetEntity="User", inversedBy="projects")
     * @ORM\JoinTable(name="project_user")
     */
    private Collection $users;


    public function __construct() {
        $this->users = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getProjectId(): int
    {
        return $this->projectId;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
       return [
           'projectId' => $this->projectId,
           'title' => $this->title
       ];
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getUsers(): ArrayCollection|Collection
    {
        return $this->users;
    }

    /**
     * @param ArrayCollection|Collection $users
     */
    public function setUsers(ArrayCollection|Collection $users): void
    {
        $this->users = $users;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addProject($this);
        }

        return $this;
    }

    /**
     * @return void
     */
    public function removeAllUsersCollection(): void
    {
        $this->users = new ArrayCollection();
    }

}
