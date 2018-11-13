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
        $em = $this->getDoctrine()->getManager();
        $entity = new User();

        $entity->setEmail($request->get('email'));
        $entity->setLastName($request->get('lastName'));
        $entity->setFirstName($request->get('firstName'));
        $entity->setState($request->get('state'));
        $entity->setCreationDate(new \DateTime());

        if (null !== $group_id = $request->get('group_id')) {
            $group = $em->getRepository(Group::class)->find($group_id);
            if (null === $group) {
                return new View([
                    'message' => 'Group does not exist.',
                ],
                    Response::HTTP_BAD_REQUEST
                );
            }
            $entity->setGroup($group);
        }

        $validator = $this->get('validator');
        $errors = $validator->validate($entity);

        if (count($errors) > 0) {
            return new View($errors, Response::HTTP_BAD_REQUEST);
        }

        $em->persist($entity);
        $em->flush();

        return new View(
            [
                'message' => 'User has been added successfully.',
                'user_id' => $entity->getId(),
            ],
            Response::HTTP_OK
        );
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

        if (null !== $lastName = $request->get('lastName')) {
            $user->setLastName($lastName);
        }

        if (null !== $firstName = $request->get('firstName')) {
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
            return new View($errors, Response::HTTP_BAD_REQUEST);
        }

        $em->flush();

        return new View(
            [
                'message' => 'User has been updated successfully.',
                'user_id' => $user->getId(),
            ],
            Response::HTTP_OK
        );
    }
}
