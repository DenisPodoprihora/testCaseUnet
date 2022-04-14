<?php

namespace App\Controller;

use App\Model\ModelInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EntityController extends AbstractController
{
    /**
     * @var ModelInterface
     */
    private ModelInterface $model;

    /**
     * @param ModelInterface $model
     */
    public function __construct(ModelInterface $model)
    {
        $this->model = $model;
    }

    /**
     * @Route("/admin/{entityName}", name="admin_entity_post", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function saveEntity(Request $request): Response
    {
        $result = $this->model->createEntity($request->request);
        if (!$result) {
            return new JsonResponse(false, 404);
        }
        return $this->redirect('admin_entity_get');
    }

    /**
     * @Route("/admin/{entityName}", name="admin_entity_get", methods={"GET"})
     *
     * @return Response
     */
    public function getTableEntity(): Response
    {
        return new JsonResponse($this->model->getEntitiesForTable());
    }

    /**
     * @Route("/admin/{entityName}/new", name="admin_entity_create", methods={"GET"})
     *
     * @return Response
     */
    public function createEntity(): Response
    {
        return new JsonResponse([$this->model->getEntityName() => []]);
    }

    /**
     * @Route("/admin/{entityName}/{id}", name="read_entity", methods={"GET"})
     *
     * @param int $id
     * @return Response
     */
    public function readEntity(int $id): Response
    {
        try {
            return new JsonResponse([$this->model->getEntityName() => $this->model->readEntity($id)]);
        } catch (\Exception $ex) {
            return new Response($ex->getMessage(), 404);
        }
    }

    /**
     * @Route("/admin/{entityName}/{id}", name="update_entity", methods={"PUT"})
     *
     * @param Request $request
     * @param int     $id
     * @return Response
     */
    public function updateEntity(Request $request, int $id): Response
    {
        try {
            return new JsonResponse($this->model->updateEntity($id, $request->request));
        } catch (\Exception $ex) {
            return new Response($ex->getMessage(), 404);
        }
    }

    /**
     * @Route("/admin/{entityName}/{id}", name="delete_entity", methods={"DELETE"})
     *
     * @param int $id
     * @return Response
     */
    public function deleteEntity(int $id): Response
    {
        try {
            return new JsonResponse($this->model->deleteEntity($id));
        } catch (\Exception $ex) {
            return new Response($ex->getMessage(), 404);
        }
    }

    /**
     * @Route("/admin/{entityName}/{id}/edit", name="admin_entity_edit", methods={"GET"})
     *
     * @param int $id
     * @return Response
     */
    public function editEntity(int $id): Response
    {
        return new JsonResponse([$this->model->getEntityName() => $this->model->readEntity($id)]);
    }

}