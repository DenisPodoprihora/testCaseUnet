<?php

namespace App\Entity;

use App\Service\DateTimeService;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Task
 *
 * @ORM\Table(name="task", indexes={@ORM\Index(name="user_id", columns={"user_id"}), @ORM\Index(name="project_id", columns={"project_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Task implements EntityInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="task_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $taskId;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(name="title", type="string", length=15, nullable=false)
     */
    private string $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="content", type="text", length=0, nullable=true)
     */
    private string $content;

    /**
     * @var string
     *
     * @Assert\Choice({"open", "in process", "review", "done"})
     *
     * @ORM\Column(name="status", type="string", length=0, nullable=false, options={"default"="open"})
     */
    private string $status = 'open';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private \DateTime $createdAt;

    /**
     * @var Project
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="project_id")
     * })
     */
    private Project $project;

    /**
     * @var User
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * })
     */
    private User $user;

    /**
     * @ORM\PrePersist
     */
    public function prePersist(): void
    {
        $this->createdAt = DateTimeService::getCurrentTime();
    }

    /**
     * @return int
     */
    public function getTaskId(): int
    {
        return $this->taskId;
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
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
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
     * @return $this
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @param Project $project
     * @return $this
     */
    public function setProject(Project $project): self
    {
        $this->project = $project;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'taskId' => $this->taskId,
            'title' => $this->title,
            'content' => $this->content,
            'project' => $this->project->toArray(),
            'user' => $this->user->toArray()
        ];
    }
}
