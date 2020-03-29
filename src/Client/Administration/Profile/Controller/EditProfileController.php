<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Profile\Controller;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Client\Administration\Controller\AbstractAdminController;
use EryseClient\Client\Administration\Profile\Voter\AdminProfileVoter;
use EryseClient\Client\Profile\Exception\ProfileNotFoundException;
use EryseClient\Client\Profile\Facade\EditProfileFacade;
use EryseClient\Client\Profile\Form\Type\EditProfileType;
use EryseClient\Client\Profile\Repository\ProfileRepository;
use EryseClient\Client\Profile\Validator\EditProfileValidator;
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
class EditProfileController extends AbstractAdminController
{
    use TranslatorTrait;

    public const ROUTE_LIST = 'administration-profiles-list';
    public const ROUTE_EDIT = 'administration-profile-edit';

    /**
     * @Route("/edit/{id}", name="administration-profile-edit")
     * @param int $id
     * @param Request $request
     * @param ProfileRepository $profileRepository
     * @param UserRepository $userRepository
     *
     * @param EditProfileFacade $editProfileFacade
     *
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ProfileNotFoundException
     */
    public function edit(
        int $id,
        Request $request,
        ProfileRepository $profileRepository,
        UserRepository $userRepository,
        EditProfileFacade $editProfileFacade
    ) : Response {
        $this->denyAccessUnlessGranted(AdminProfileVoter::EDIT);
        $profile = $profileRepository->find($id);

        if (!$profile) {
            throw new ProfileNotFoundException();
        }

        $profile->setUser($userRepository->find($profile->getUserId()));

        $editProfileValidator = EditProfileValidator::fromProfile($profile);
        $profileForm = $this->createForm(EditProfileType::class, $editProfileValidator);
        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $editProfileFacade->updateProfile($editProfileValidator, $profile);

            $this->addFlash(FlashType::SUCCESS, $this->translator->trans('profile.edit.success', [], 'administration'));
        }

        $bcs = $this->getAdminControllerBreadcrumb()->addNewItem(
            $this->translator->trans('breadcrumb.profile.list', [], 'administration'),
            self::ROUTE_LIST
        )->addNewItem($profile->getUser()->getUsername());

        return $this->render(
            'Administration/Profile/Profile/edit.html.twig',
            [
                'bcs' => $bcs,
                'profile' => $profile,
                'profileUser' => $profile->getUser(),
                'profileForm' => $profileForm->createView()
            ]
        );
    }

}
