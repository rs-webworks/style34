<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Profile\Controller;

use EryseClient\Client\Administration\Profile\Voter\AdminProfileVoter;
use EryseClient\Client\Profile\Facade\ProfileFacade;
use EryseClient\Client\Profile\Form\Type\ProfileSearchType;
use EryseClient\Common\Controller\AbstractController;
use EryseClient\Server\User\Form\Type\UserType;
use EryseClient\Server\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RoleController
 *
 * @package EryseClient\Controller\Administration\User
 */
class ProfileController extends AbstractController
{

    /**
     * @Route("/administration/profiles/",name="administration-profiles-list")
     * @param Request $request
     *
     * @param ProfileFacade $profileFacade
     *
     * @return Response
     */
    public function list(Request $request, ProfileFacade $profileFacade)
    {
        $this->denyAccessUnlessGranted(AdminProfileVoter::VIEW);

        $searchForm = $this->createForm(ProfileSearchType::class);
        $searchForm->handleRequest($request);

        $profiles = $profileFacade->getProfilesPaginated($searchForm, $this->getPageParam($request));

        return $this->render(
            'Administration/Profile/Profile/index.html.twig',
            ["profiles" => $profiles, "searchForm" => $searchForm->createView()]
        );
    }

    /**
     * @Route("/administration/profile/{id}", name="administration-profile-view")
     * @param $id
     * @param UserRepository $userRepository
     *
     * @return Response
     */
    public function view($id, UserRepository $userRepository)
    {
        $user = $userRepository->find($id);
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->remove("username");

        return $this->render(
            'Administration/User/User/view.html.twig',
            ["user" => $user, "userForm" => $userForm->createView()]
        );
    }
}
