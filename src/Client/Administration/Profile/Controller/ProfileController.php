<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Profile\Controller;

use EryseClient\Client\Administration\Profile\Voter\AdminProfileVoter;
use EryseClient\Client\Profile\Facade\ProfileFacade;
use EryseClient\Client\Profile\Form\Type\ProfileSearchType;
use EryseClient\Client\Profile\Repository\ProfileRepository;
use EryseClient\Common\Breadcrumb\Entity\Breadcrumb;
use EryseClient\Common\Breadcrumb\Entity\BreadcrumbItem;
use EryseClient\Common\Controller\AbstractController;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Server\User\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
    use TranslatorAwareTrait;

    public const ROUTE_LIST = "administration-profiles-list";

    /**
     * @Route("/administration/profiles/",name=ProfileController::ROUTE_LIST)
     * @param Request $request
     * @param ProfileFacade $profileFacade
     * @param UserRepository $userRepository
     *
     * @return Response
     */
    public function list(Request $request, ProfileFacade $profileFacade, UserRepository $userRepository)
    {

        $this->denyAccessUnlessGranted(AdminProfileVoter::VIEW);
        $breadcrumb = $this->getControllerBreadcrumb();
        $breadcrumb->addItem(
            new BreadcrumbItem(
                self::ROUTE_LIST, $this->translator->trans("breadcrumb.profile.list", [], "administration")
            )
        );

        $searchForm = $this->createForm(ProfileSearchType::class);
        $searchForm->handleRequest($request);

        $profiles = $profileFacade->getProfilesPaginated(
            $searchForm,
            $this->getPageParam($request),
            $request->get("role")
        );

        return $this->render(
            'Administration/Profile/Profile/list.html.twig',
            [
                "profiles" => $profiles,
                "userRepository" => $userRepository,
                "searchForm" => $searchForm->createView(),
                "breadcrumb" => $breadcrumb
            ]
        );
    }

    /**
     * @Route("/administration/profile/edit/{id}", name="administration-profile-edit")
     * @param int $id
     * @param Request $request
     * @param ProfileRepository $profileRepository
     *
     * @param UserRepository $userRepository
     *
     * @return Response
     */
    public function edit(
        int $id,
        Request $request,
        ProfileRepository $profileRepository,
        UserRepository $userRepository
    ) {
        $this->denyAccessUnlessGranted(AdminProfileVoter::EDIT);

        $profile = $profileRepository->find($id);

        return $this->render(
            'Administration/Profile/Profile/edit.html.twig',
            ["profile" => $profile, "profileUser" => $userRepository->find($profile->getUserId())]
        );
    }

    /**
     *
     */
    public function getControllerBreadcrumb(): Breadcrumb
    {
        $breadcrumb = new Breadcrumb();

        $breadcrumb->addItem(
            new BreadcrumbItem("home-index", $this->getParameter("eryse.client.name"))
        );

        $breadcrumb->addItem(
            new BreadcrumbItem("administration-dashboard", $this->translator->trans("dashboard", [], "administration"))
        );

        return $breadcrumb;
    }
}
