<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Profile\Controller;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Client\Administration\Controller\AbstractAdminController;
use EryseClient\Client\Administration\Profile\Voter\AdminProfileVoter;
use EryseClient\Client\Profile\Exception\ProfileNotFoundException;
use EryseClient\Client\Profile\Facade\EditProfileFacade;
use EryseClient\Client\Profile\Facade\ListProfileFacade;
use EryseClient\Client\Profile\Form\Type\ProfileSearchType;
use EryseClient\Client\Profile\Form\Type\EditProfileType;
use EryseClient\Client\Profile\Repository\ProfileRepository;
use EryseClient\Client\Profile\Validator\ProfileValidator;
use EryseClient\Common\Entity\FlashType;
use EryseClient\Server\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorTrait;

/**
 * Class ProfileController
 * @Route("/administration/profile")
 */
class ListProfileController extends AbstractAdminController
{
    use TranslatorTrait;

    public const ROUTE_LIST = 'administration-profiles-list';
    public const ROUTE_EDIT = 'administration-profile-edit';

    /**
     * @Route("/list",name="administration-profiles-list")
     * @param Request $request
     * @param ListProfileFacade $profileFacade
     * @param UserRepository $userRepository
     *
     * @return Response
     */
    public function list(
        Request $request,
        ListProfileFacade $profileFacade,
        UserRepository $userRepository
    ) : Response {
        $this->denyAccessUnlessGranted(AdminProfileVoter::VIEW);
        $bcs = $this->getAdminControllerBreadcrumb()->addNewItem(
            $this->translator->trans('breadcrumb.profile.list', [], 'administration'),
            self::ROUTE_LIST
        );

        $searchForm = $this->createForm(ProfileSearchType::class);
        $searchForm->handleRequest($request);

        $profiles = $profileFacade->getProfilesPaginated(
            $searchForm,
            $this->getPageParam($request),
            $request->get('role'),
            (bool)$request->get('displayHidden')
        );

        return $this->render(
            'Administration/Profile/Profile/list.html.twig',
            [
                'profiles' => $profiles,
                'userRepository' => $userRepository,
                'searchForm' => $searchForm->createView(),
                'bcs' => $bcs,
                'displayHidden' => (bool)$request->get('displayHidden')
            ]
        );
    }

}
