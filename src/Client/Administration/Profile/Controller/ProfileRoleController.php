<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Profile\Controller;

use EryseClient\Client\ProfileRole\Repository\ProfileRoleRepository;
use EryseClient\Common\Controller\ControllerSettings;
use EryseClient\Server\User\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RoleController
 * @package EryseClient\Controller\Administration\User
 */
class ProfileRoleController extends AbstractController
{

    /**
     * @Route("/administration/profile/roles",name="administration-profile-role-index")
     * @param ProfileRoleRepository $roleRepository
     * @return Response
     */
    public function index(ProfileRoleRepository $roleRepository)
    {
        $roles = $roleRepository->findAll();
        return $this->render('Administration/Profile/Role/index.html.twig', ["roles" => $roles]);
    }

    /**
     * @Route("/administration/profile/roles/profiles/{role}", name="administration-profile-role-users")
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
        $qb = $userRepository->createQueryBuilder('p')
            ->where('p.role = :role')
            ->orderBy("p.id")
            ->setParameter('role', $role->getName());

        $users = $paginator->paginate(
            $qb,
            $request->query->get("page") ? (int) $request->query->get("page") : 1,
            ControllerSettings::PAGINATOR_DEFAULT_IPP
        );

        return $this->render('Administration/Profile/Role/profiles.html.twig', ["users" => $users, "role" => $role]);
    }
}
