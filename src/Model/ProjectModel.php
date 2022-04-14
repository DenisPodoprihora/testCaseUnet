<?php

namespace App\Model;

use App\Entity\EntityInterface;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProjectModel extends AbstractModel
{

    protected static string $ENTITY_NAME = 'project';

    /**
     * @var UserRepository
     */
    protected UserRepository $userRepository;

    /**
     * @param ValidatorInterface $validator
     * @param ProjectRepository  $repository
     * @param UserRepository     $userRepository
     */
    public function __construct(
        ValidatorInterface $validator,
        ProjectRepository $repository,
        UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        parent::__construct($validator, $repository);
    }

    /**
     * @param Project $entity
     * @param ParameterBag    $data
     * @return void
     */
    protected function setEntityValues(EntityInterface $entity, ParameterBag $data): void
    {
        !$data->has('title') ?: $entity->setTitle($data->get('title'));

        !$data->has('users') ?: $entity->removeAllUsersCollection();
        foreach ($data->all('users') as $user) {
            /** @var User $userEntity */
            $userEntity = $this->userRepository->getEntityObject((int)$user);
            if ($userEntity === null) {
                continue;
            }
            $userEntity->addProject($entity);
            $entity->addUser($userEntity);
        }
    }
}