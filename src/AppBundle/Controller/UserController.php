<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Group;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @package AppBundle\Controller
 */
class UserController extends FOSRestController
{
    /**
     * @Rest\Get("/users/")
     */
    public function getAction()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        if ($users === null) {
            return new View('there are no users exist', Response::HTTP_NOT_FOUND);
        }

        return new View($users, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return View
     * @Rest\Post("/users/")
     */
    public function postAction(Request $request)
    {
        $entity = new User();

        $entity->setEmail($request->get('email'));
        $entity->setLastName($request->get('last_name'));
        $entity->setFirstName($request->get('first_name'));
        $entity->setState($request->get('state'));
        $entity->setCreationDate(new \DateTime());

        $validator = $this->get('validator');
        $errors = $validator->validate($entity);

        if (count($errors) > 0) {
            $errorsString = (string)$errors;

            return new View($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new View('User has been added successfully', Response::HTTP_OK);
    }

    /**
     * @param $id
     * @return View
     * @Rest\Get("/users/{id}/")
     */
    public function idAction($id)
    {
        $user = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        if (null === $user) {
            return new View('User not found', Response::HTTP_NOT_FOUND);
        }

        return new View($user, Response::HTTP_OK);
    }

    /**
     * @param         $id
     * @param Request $request
     * @Rest\Put("/users/{id}/")
     * @return View
     */
    public function updateAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em
            ->getRepository(User::class)
            ->find($id);

        if (null === $user) {
            return new View('User not found', Response::HTTP_NOT_FOUND);
        }

        if (null !== $email = $request->get('email')) {
            $user->setEmail($email);
        }

        if (null !== $lastName = $request->get('last_name')) {
            $user->setLastName($lastName);
        }

        if (null !== $firstName = $request->get('first_name')) {
            $user->setFirstName($firstName);
        }

        if (null !== $state = $request->get('state')) {
            $user->setState($state);
        }

        if (null !== $group_id = $request->get('group_id')) {
            $group = $em->getRepository(Group::class)->find($group_id);
            if (null === $group) {
                return new View('Group does not exist', Response::HTTP_BAD_REQUEST);
            }
            $user->setGroup($group);
        }

        $validator = $this->get('validator');
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorsString = (string)$errors;

            return new View($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $em->flush();

        return new View('User has been updated successfullly', Response::HTTP_OK);
    }
}
