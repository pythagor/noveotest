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
            return new View($errors, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new View(
            [
                'message'  => 'Group has been added successfully.',
                'group_id' => $entity->getId(),
            ],
            Response::HTTP_OK
        );
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

        $groupRepository = $em->getRepository(Group::class);
        $group = $groupRepository->find($id);

        if (null === $group) {
            return new View('Group not found', Response::HTTP_NOT_FOUND);
        }

        if (null !== $name = $request->get('name')) {
            $group->setName($name);
        }

        if (
            (null !== $userIds = $request->get('users_list')) &&
            is_array($userIds)
        ) {
            $userRepository = $em->getRepository(User::class);
            $usersInGroup = $groupRepository->getUserIds($group->getId());
            $usersInGroupFiltered = $userRepository->addUsersToGroups(
                $group,
                $userIds,
                $usersInGroup
            );

            $userRepository->clearGroupForUsers($usersInGroupFiltered);
        }

        $validator = $this->get('validator');
        $errors = $validator->validate($group);

        if (count($errors) > 0) {
            return new View($errors, Response::HTTP_BAD_REQUEST);
        }

        $em->flush();

        return new View(
            [
                'message'  => 'Group has been updated successfully.',
                'group_id' => $group->getId(),
            ],
            Response::HTTP_OK
        );
    }
}
