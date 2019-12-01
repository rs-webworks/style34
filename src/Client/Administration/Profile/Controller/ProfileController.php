<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Profile\Controller;

use EryseClient\Client\Administration\Profile\Voter\AdminProfileVoter;
use EryseClient\Client\Profile\Facade\ProfileFacade;
use EryseClient\Client\Profile\Form\Type\ProfileSearchType;
use EryseClient\Common\Controller\AbstractController;
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

        $profiles = $profileFacade->getProfilesPaginated(
            $searchForm,
            $this->getPageParam($request),
            $request->get("role")
        );

        return $this->render(
            'Administration/Profile/Profile/list.html.twig',
            ["profiles" => $profiles, "searchForm" => $searchForm->createView()]
        );
    }
}
