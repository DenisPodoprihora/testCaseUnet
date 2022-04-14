<?php

namespace App\Model;

use App\Entity\EntityInterface;
use App\Entity\Exception\CannotFindEntityException;
use App\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractModel implements ModelInterface
{
    /** @var ValidatorInterface */
    protected ValidatorInterface $validator;

    /** @var RepositoryInterface */
    protected RepositoryInterface $repository;

    /** @var string  */
    protected static string $ENTITY_NAME;

    /**
     * @param ValidatorInterface  $validator
     * @param RepositoryInterface $repository
     */
    public function __construct(
        ValidatorInterface $validator,
        RepositoryInterface $repository
    )
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    public function createEntity(ParameterBag $data): bool
    {
        /** @var EntityInterface $entity */
        $entity = $this->repository->getEntityObject();

        $this->processEntity($entity, $data);

        return $this->repository->save($entity);
    }

    /**
     * @param int $entityId
     * @return array
     * @throws CannotFindEntityException
     */
    public function readEntity(int $entityId): array
    {
        $entity = $this->repository->getEntityObject($entityId);
        if ($entity === null) {
            throw new CannotFindEntityException("There are no entity {$this->getEntityName()} with id {$entityId}");
        }
        return $entity->toArray();
    }

    /**
     * @param int          $entityId
     * @param ParameterBag $data
     * @return bool
     * @throws CannotFindEntityException
     */
    public function updateEntity(int $entityId, ParameterBag $data): bool
    {
        /** @var EntityInterface $entity */
        $entity = $this->repository->getEntityObject($entityId);

        if ($entity === null) {
            throw new CannotFindEntityException("There are no entity {$this->getEntityName()} with id {$entityId}");
        }

        $this->processEntity($entity, $data);

        return $this->repository->save($entity);
    }

    /**
     * @param int $entityId
     * @return bool
     * @throws CannotFindEntityException
     */
    public function deleteEntity(int $entityId): bool
    {
        $entity = $this->repository->getEntityObject($entityId);
        if ($entity === null) {
            throw new CannotFindEntityException("There are no entity {$this->getEntityName()} with id {$entityId}");
        }

        return $this->repository->delete($entity);
    }

    /**
     * @return array
     */
    public function getEntitiesForTable(): array
    {
        return $this->repository->getAllRows('t');
    }

    /**
     * @param EntityInterface $entity
     * @param ParameterBag    $data
     * @return void
     */
    private function processEntity(EntityInterface $entity, ParameterBag $data): void
    {
        $this->setEntityValues($entity, $data);
        $errors = $this->validator->validate($entity);

        if ($errors->count() > 0) {
            throw new ValidatorException((string) $errors);
        }
    }

    abstract protected function setEntityValues(EntityInterface $entity, ParameterBag $data): void;

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return static::$ENTITY_NAME;
    }
}