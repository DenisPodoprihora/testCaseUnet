<?php

namespace App\Model;

use App\Entity\EntityInterface;
use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskModel extends AbstractModel
{
    protected static string $ENTITY_NAME = 'task';

    /**
     * @var UserRepository
     */
    protected UserRepository $userRepository;

    /**
     * @var ProjectRepository
     */
    protected ProjectRepository $projectRepository;

    /**
     * @param ValidatorInterface $validator
     * @param TaskRepository     $repository
     * @param UserRepository     $userRepository
     * @param ProjectRepository  $projectRepository
     */
    public function __construct(
        ValidatorInterface $validator,
        TaskRepository $repository,
        UserRepository $userRepository,
        ProjectRepository $projectRepository)
    {
        $this->userRepository = $userRepository;
        $this->projectRepository = $projectRepository;
        parent::__construct($validator, $repository);
    }

    /**
     * @param EntityInterface|Task $entity
     * @param ParameterBag    $data
     * @return void
     */
    protected function setEntityValues(EntityInterface $entity, ParameterBag $data): void
    {
        if ($data->has('userId')) {
            /** @var User $userEntity */
            $userEntity = $this->userRepository->getEntityObject((int)$data->get('userId'));
            if ($userEntity === null) {
                throw new UserNotFoundException();
            }
            $entity->setUser($userEntity);
        }

        if ($data->has('projectId')) {
            /** @var Project $projectEntity */
            $projectEntity = $this->projectRepository->getEntityObject((int)$data->get(('projectId')));
            if ($projectEntity === null) {
                throw new \RuntimeException('project not find with id: ' . $data->get('projectId'));
            }
            $entity->setProject($projectEntity);
        }

        !$data->has('content') ?: $entity->setContent($data->get('content'));
        !$data->has('title') ?: $entity->setTitle($data->get('title'));

        ($this->isValidStatus($entity, $data->get('status'))) ?: $entity->setStatus($data->get('status'));
    }

    /**
     * @param Task        $task
     * @param string|null $status
     * @return bool
     */
    private function isValidStatus(Task $task, ?string $status): bool
    {
        if ($status === null) {
            return false;
        }
        $prevValue = $task->getStatus();
        switch ($status) {
            case 'open': {
                return ($prevValue === 'review' || $prevValue === 'open');
            }
            case 'in process': {
                return $prevValue === 'open';
            }
            case 'review': {
                return $prevValue === 'in process';
            }
            case 'done': {
                return $prevValue === 'review';
            }
            default: return false;
        }
    }
}