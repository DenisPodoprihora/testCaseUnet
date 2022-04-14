<?php

namespace App\Repository;

use App\Entity\EntityInterface;
use Doctrine\ORM\EntityManager;

interface RepositoryInterface
{
    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager;

    /**
     * @param mixed ...$params
     * @return EntityInterface|null
     */
    public function getEntityObject(...$params): ?EntityInterface;

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function delete(EntityInterface $entity): bool;

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function save(EntityInterface $entity): bool;

    /**
     * @return null|string
     */
    public function getLastError(): ?string;

    /**
     * @param string $alias
     * @return array
     */
    public function getAllRows(string $alias): array;
}