<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Profile\Controller;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Client\Administration\Profile\Voter\AdminProfileVoter;
use EryseClient\Client\Profile\Facade\ProfileFacade;
use EryseClient\Client\Profile\Form\Type\ProfileSearchType;
use EryseClient\Client\Profile\Form\Type\ProfileType;
use EryseClient\Client\Profile\Repository\ProfileRepository;
use EryseClient\Common\Controller\AbstractController;
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
    public const ROUTE_LIST = "administration-profiles-list";
    public const ROUTE_EDIT = "administration-profile-edit";

    /**
     * @Route("/administration/profiles",name="administration-profiles-list")
     * @param Request $request
     * @param ProfileFacade $profileFacade
     * @param UserRepository $userRepository
     *
     * @return Response
     */
    public function list(
        Request $request,
        ProfileFacade $profileFacade,
        UserRepository $userRepository
    ) {
        $this->denyAccessUnlessGranted(AdminProfileVoter::VIEW);
        $bcs = $this->getAdminControllerBreadcrumb()->addNewItem(
            $this->translator->trans("breadcrumb.profile.list", [], "administration"),
            self::ROUTE_LIST
        );

        $searchForm = $this->createForm(ProfileSearchType::class);
        $searchForm->handleRequest($request);

        $profiles = $profileFacade->getProfilesPaginated(
            $searchForm,
            $this->getPageParam($request),
            $request->get("role"),
            (bool) $request->get("displayHidden")
        );

        return $this->render(
            'Administration/Profile/Profile/list.html.twig',
            [
                "profiles" => $profiles,
                "userRepository" => $userRepository,
                "searchForm" => $searchForm->createView(),
                "bcs" => $bcs,
                "displayHidden" => (bool) $request->get("displayHidden")
            ]
        );
    }

    /**
     * @Route("/administration/profile/edit/{id}", name="administration-profile-edit")
     * @param int $id
     * @param Request $request
     * @param ProfileRepository $profileRepository
     * @param UserRepository $userRepository
     * @param ProfileFacade $profileFacade
     *
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function edit(
        int $id,
        Request $request,
        ProfileRepository $profileRepository,
        UserRepository $userRepository,
        ProfileFacade $profileFacade
    ) {
        $this->denyAccessUnlessGranted(AdminProfileVoter::EDIT);

        $profile = $profileRepository->find($id);
        $profile->setUser($userRepository->find($profile->getUserId()));

        $bcs = $this->getAdminControllerBreadcrumb()->addNewItem(
            $this->translator->trans("breadcrumb.profile.list", [], "administration"),
            self::ROUTE_LIST
        )->addNewItem($profile->getUser()->getUsername());

        $profileForm = $this->createForm(ProfileType::class, $profile);
        $profileForm->handleRequest($request);

        $profileFacade->saveProfile($profileForm);

        return $this->render(
            'Administration/Profile/Profile/edit.html.twig',
            [
                "profile" => $profile,
                "bcs" => $bcs,
                "profileUser" => $profile->getUser(),
                "profileForm" => $profileForm->createView()
            ]
        );
    }

}
