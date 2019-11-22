<?php declare(strict_types=1);

namespace EryseClient\Component\Client\Administration\User\Controller;

use EryseClient\Component\Common\Controller\ControllerSettings;
use EryseClient\Model\Client\ProfileRole\Repository\ProfileRoleRepository;
use EryseClient\Model\Server\User\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RoleController
 * @package EryseClient\Controller\Administration\User
 * @IsGranted(EryseClient\Model\Server\UserRole\Entity\UserRole::ADMIN)
 */
class RoleController extends AbstractController
{

    /**
     * @Route("/administration/user/roles",name="administration-user-role-index")
     * @param ProfileRoleRepository $roleRepository
     * @return Response
     */
    public function index(ProfileRoleRepository $roleRepository)
    {
        $roles = $roleRepository->findAll();
        return $this->render('Administration/User/Role/index.html.twig', ["roles" => $roles]);
    }

    /**
     * @Route("/administration/user/roles/users/{role}", name="administration-user-role-users")
     * @param string $role
     * @param Request $request
     * @param ProfileRoleRepository $roleRepository
     * @param UserRepository $userRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function roleUsers(
        string $role,
        Request $request,
        ProfileRoleRepository $roleRepository,
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
