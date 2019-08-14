<?php declare(strict_types=1);

namespace EryseClient\Controller\Administration\User;

use EryseClient\Controller\ControllerSettings;
use EryseClient\Repository\Client\User\RoleRepository;
use EryseClient\Repository\Server\User\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RoleController
 * @package EryseClient\Controller\Administration\User
 * @IsGranted(EryseClient\Entity\Client\User\Role::ADMIN)
 */
class RoleController extends AbstractController
{

    /**
     * @Route("/administration/user/roles",name="administration-user-role-index")
     */
    public function index(RoleRepository $roleRepository)
    {
        $roles = $roleRepository->findAll();
        return $this->render('Administration/User/Role/index.html.twig', ["roles" => $roles]);
    }

    /**
     * @Route("/administration/user/roles/users/{role}", name="administration-user-role-users")
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
            ->where('u.roles LIKE :role')
            ->orderBy("u.id")
            ->setParameter('role', '%' . $role->getName() . '%');

        $users = $paginator->paginate(
            $qb,
            $request->query->get("page") ? (int) $request->query->get("page") : 1,
            ControllerSettings::PAGINATOR_DEFAULT_IPP
        );

        return $this->render('Administration/User/Role/users.html.twig', ["users" => $users, "role" => $role]);
    }



}