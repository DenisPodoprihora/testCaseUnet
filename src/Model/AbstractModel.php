<?php

namespace App\Model;

use App\Entity\EntityInterface;
use App\Entity\Exception\CannotFindEntityException;
use App\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractModel extends AbstractAdminModel
{
    public function createEntity(ParameterBag $data): bool
    {
        // TODO: for ROLE_USER users
        return false;
    }

    /**
     * @param int $entityId
     * @return array
     */
    public function readEntity(int $entityId): array
    {
        // TODO: for ROLE_USER users
        return [];
    }

    /**
     * @param int          $entityId
     * @param ParameterBag $data
     * @return bool
     */
    public function updateEntity(int $entityId, ParameterBag $data): bool
    {
        // TODO: for ROLE_USER users
        return false;
    }

    /**
     * @param int $entityId
     * @return bool
     */
    public function deleteEntity(int $entityId): bool
    {
        // TODO: for ROLE_USER users
        return false;
    }

    /**
     * @return array
     */
    public function getEntitiesForTable(): array
    {
        // TODO: for ROLE_USER users
        return [];
    }
}