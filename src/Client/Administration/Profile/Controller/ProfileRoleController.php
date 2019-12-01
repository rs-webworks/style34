<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Profile\Controller;

use EryseClient\Client\Profile\Repository\ProfileRepository;
use EryseClient\Client\ProfileRole\Repository\ProfileRoleRepository;
use EryseClient\Common\Controller\ControllerSettings;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RoleController
 *
 * @package EryseClient\Controller\Administration\User
 */
class ProfileRoleController extends AbstractController
{

    /**
     * @Route("/administration/profile/roles",name="administration-profile-role-list")
     * @param ProfileRoleRepository $roleRepository
     *
     * @return Response
     */
    public function list(ProfileRoleRepository $roleRepository)
    {
        $roles = $roleRepository->findAll();
        return $this->render('Administration/Profile/Role/list.html.twig', ["roles" => $roles]);
    }

    /**
     * @Route("/administration/profile/roles/profiles/{role}", name="administration-profile-role-users")
     * @param string $role
     * @param Request $request
     * @param ProfileRoleRepository $roleRepository
     * @param ProfileRepository $profileRepository
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function roleUsers(
        string $role,
        Request $request,
        ProfileRoleRepository $roleRepository,
        ProfileRepository $profileRepository,
        PaginatorInterface $paginator
    ) {
        $role = $roleRepository->findOneBy(["name" => $role]);
        $qb = $profileRepository->createQueryBuilder('p')->where('p.role = :role')->orderBy("p.id")->setParameter(
            'role',
            $role
        );

        $profiles = $paginator->paginate(
            $qb,
            $request->query->get("page") ? (int) $request->query->get("page") : 1,
            ControllerSettings::PAGINATOR_DEFAULT_IPP
        );

        return $this->render(
            'Administration/Profile/Role/profiles.html.twig',
            ["profiles" => $profiles, "role" => $role]
        );
    }
}
