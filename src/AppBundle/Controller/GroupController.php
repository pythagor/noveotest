<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Group;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GroupController
 * @package AppBundle\Controller
 */
class GroupController extends FOSRestController
{
    /**
     * @Rest\Get("/groups/")
     */
    public function getAction()
    {
        $groups = $this->getDoctrine()->getRepository(Group::class)->findAll();
        if ($groups === null) {
            return new View('there are no users exist', Response::HTTP_NOT_FOUND);
        }

        return $groups;
    }

    /**
     * @param Request $request
     * @return View
     * @Rest\Post("/groups/")
     */
    public function postAction(Request $request)
    {
        $entity = new Group();

        $entity->setName($request->get('name'));

        $validator = $this->get('validator');
        $errors = $validator->validate($entity);

        if (count($errors) > 0) {
            $errorsString = (string)$errors;

            return new View($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new View('Group has been added successfully', Response::HTTP_OK);
    }

    /**
     * @param         $id
     * @param Request $request
     * @Rest\Put("/groups/{id}/")
     * @return View
     */
    public function updateAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em
            ->getRepository(Group::class)
            ->find($id);

        if (null === $group) {
            return new View('Group not found', Response::HTTP_NOT_FOUND);
        }

        if (null !== $name = $request->get('name')) {
            $group->setName($name);
        }

        $validator = $this->get('validator');
        $errors = $validator->validate($group);

        if (count($errors) > 0) {
            $errorsString = (string)$errors;

            return new View($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $em->flush();

        return new View('Group has been updated successfullly', Response::HTTP_OK);
    }
}
