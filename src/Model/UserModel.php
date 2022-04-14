<?php

namespace App\Model;

use App\Entity\EntityInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserModel extends AbstractModel
{

    protected static string $ENTITY_NAME = 'user';

    /**
     * @var UserPasswordHasherInterface
     */
    protected UserPasswordHasherInterface $passwordEncoder;

    /**
     * @param ValidatorInterface          $validator
     * @param UserRepository              $repository
     * @param UserPasswordHasherInterface $passwordEncoder
     */
    public function __construct(
        ValidatorInterface $validator,
        UserRepository $repository,
        UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct($validator, $repository);
    }

    /**
     * @param EntityInterface|User $entity
     * @param ParameterBag    $data
     * @return void
     */
    protected function setEntityValues(EntityInterface $entity, ParameterBag $data): void
    {
        !$data->has('login') ?: $entity->setLogin($data->get('login'));
        !$data->has('password') ?: $entity->setPassword($this->passwordEncoder->hashPassword($entity, $data->get('password')));

        !$data->has('roles') ?: $entity->setRoles($data->get('roles', []));
    }
}