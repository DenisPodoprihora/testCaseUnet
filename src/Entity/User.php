<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="login", columns={"login"})})
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements EntityInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $userId;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(name="login", type="string", length=255, nullable=false)
     */
    private string $login;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private string $password;

    /**
     * @var array|null
     *
     * @ORM\Column(name="roles", type="json", nullable=true)
     */
    private ?array $roles;


    /**
     * @var Collection|Project[]
     *
     * @ORM\ManyToMany(targetEntity="Project", inversedBy="users")
     * @ORM\JoinTable(
     *     name="project_user",
     *     joinColumns={
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     *     },
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="project_id", referencedColumnName="project_id")
     *     }
     * )
     */
    private Collection $projects;

    public function __construct() {
        $this->projects = new ArrayCollection();
    }

    public function toArray(): array
    {
       return [
           'userId' => $this->userId,
           'login' => $this->password,
           'roles' => $this->roles
       ];
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function eraseCredentials()
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @param array $roles
     * @return void
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return Project[]|Collection
     */
    public function getProjects(): array|Collection
    {
        return $this->projects;
    }

    /**
     * @param Project[]|Collection $projects
     */
    public function setProjects(array|Collection $projects): void
    {
        $this->projects = $projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->addUser($this);
        }
        return $this;
    }
}
