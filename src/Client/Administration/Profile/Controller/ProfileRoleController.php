<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Profile\Controller;

use EryseClient\Client\Profile\Repository\ProfileRepository;
use EryseClient\Client\Profile\Role\Repository\RoleRepository;
use EryseClient\Common\Controller\ControllerSettings;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfileRoleController
 */
class ProfileRoleController extends AbstractController
{

    /**
     * @Route("/administration/profile/roles",name="administration-profile-role-list")
     * @param RoleRepository $roleRepository
     *
     * @return Response
     */
    public function list(RoleRepository $roleRepository) : Response
    {
        $roles = $roleRepository->findAll();
        return $this->render('Administration/Profile/Role/list.html.twig', ['roles' => $roles]);
    }

    /**
     * @Route("/administration/profile/roles/profiles/{role}", name="administration-profile-role-users")
     * @param string $role
     * @param Request $request
     * @param RoleRepository $roleRepository
     * @param ProfileRepository $profileRepository
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function roleUsers(
        string $role,
        Request $request,
        RoleRepository $roleRepository,
        ProfileRepository $profileRepository,
        PaginatorInterface $paginator
    ) : Response {
        $roleEntity = $roleRepository->findOneBy(['name' => $role]);
        $qb = $profileRepository->createQueryBuilder('p')->where('p.role = :role')->orderBy('p.id')->setParameter(
            'role',
            $roleEntity
        );

        $profiles = $paginator->paginate(
            $qb,
            $request->query->get('page') ? (int) $request->query->get('page') : 1,
            ControllerSettings::PAGINATOR_DEFAULT_IPP
        );

        return $this->render(
            'Administration/Profile/Role/profiles.html.twig',
            ['profiles' => $profiles, 'role' => $roleEntity]
        );
    }
}
