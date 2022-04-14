<?php

namespace App\Controller\Factory;

use App\Controller\EntityController;
use App\Controller\Exception\CannotBuildControllerException;
use App\Model\ModelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ControllerFactory
{
    /**
     * @param ContainerInterface $container
     * @return EntityController
     * @throws CannotBuildControllerException
     */
    public static function create(ContainerInterface $container): EntityController
    {
        $requestUrl = Request::createFromGlobals()->getPathInfo();
        $routeParams = $container->get('router')->match($requestUrl);
        $entityName = $routeParams['entityName'] ?? null;
        $entityName = preg_replace('~\?.*$~', '', $entityName);

        if (null === $entityName) {
            throw new CannotBuildControllerException(' Required parameter "entityName" is absent');
        }

        $modelName = static::getAdminPanelModelNameFullStatic($entityName);

        try {
            /** @var ModelInterface $model */
            $model = $container->get($modelName);
        } catch (ServiceNotFoundException $ex) {
            throw new NotFoundHttpException();
        }

        return new EntityController($model);
    }

    /**
     * @param string $modelName
     * @return string
     */
    public static function getAdminPanelModelNameFullStatic(string $modelName): string
    {
        $preparedModelName = ucfirst($modelName);
        return "App\Model\\{$preparedModelName}Model";
    }
}