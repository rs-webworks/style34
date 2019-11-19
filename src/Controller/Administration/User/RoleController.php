<?php declare(strict_types=1);

namespace EryseClient\Controller\Administration\User;

use EryseClient\Controller\ControllerSettings;
use EryseClient\Repository\Client\User\RoleRepository;
use EryseClient\Repository\Server\User\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RoleController
 * @package EryseClient\Controller\Administration\User
 * @IsGranted(EryseClient\Entity\Client\Profile\Role::ADMIN)
 */
class RoleController extends AbstractController
{

    /**
     * @Route("/administration/user/roles",name="administration-user-role-index")
     * @param RoleRepository $roleRepository
     * @return Response
     */
    public function index(RoleRepository $roleRepository)
    {
        $roles = $roleRepository->findAll();
        return $this->render('Administration/User/Role/index.html.twig', ["roles" => $roles]);
    }

    /**
     * @Route("/administration/user/roles/users/{role}", name="administration-user-role-users")
     * @param string $role
     * @param Request $request
     * @param RoleRepository $roleRepository
     * @param UserRepository $userRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function roleUsers(
        string $role,
        Request $request,
        RoleRepository $roleRepository,
        UserRepository $userRepository,
        PaginatorInterface $paginator
    ) {
        $role = $roleRepository->findOneBy(["name" => $role]);
        $qb = $userRepository->createQueryBuilder('u')
            ->where('u.role = :role')
            ->orderBy("u.id")
            ->setParameter('role', $role->getName());

        $users = $paginator->paginate(
            $qb,
            $request->query->get("page") ? (int) $request->query->get("page") : 1,
            ControllerSettings::PAGINATOR_DEFAULT_IPP
        );

        return $this->render('Administration/User/Role/users.html.twig', ["users" => $users, "role" => $role]);
    }
}
