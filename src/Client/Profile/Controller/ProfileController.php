<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Controller;

use EryseClient\Client\Profile\Dto\ProfileDetailDto;
use EryseClient\Client\Profile\Repository\ProfileRepository;
use EryseClient\Client\Profile\Service\ProfileService;
use EryseClient\Common\Utility\EryseUserAwareTrait;
use EryseClient\Common\Utility\LoggerAwareTrait;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
/**
 * Class UserController
 *
 *
 */
class ProfileController extends AbstractController
{
    use TranslatorAwareTrait;
    use LoggerAwareTrait;
    use EryseUserAwareTrait;

    /**
     * @IsGranted(EryseClient\Server\User\Role\Entity\RoleEntity::USER)
     * @Route("/profile/view/{username}", name="profile-view")
     * @param ProfileRepository $profileRepository
     * @param ProfileService $profileService
     * @param string $username
     *
     * @return Response
     */
    public function view(ProfileRepository $profileRepository, ProfileService $profileService, string $username) : Response
    {
        $profile = $profileRepository->findOneByUsername($username);
        $ownProfile = false;

        if ($this->user) {
            $ownProfile = $this->user->getProfile()->getId() === $profile->getId();
        }

        if (!$profileService->isDisplayable($profile)) {
            throw $this->createNotFoundException();
        }

        $detail = new ProfileDetailDto();
        $detail->profile = $profile;
        $detail->ownProfile = $ownProfile;

        return $this->render(
            'Profile/view.html.twig', [
            'detail' => $detail,
        ]);
    }

    /**
     * @Route("/profile/list", name="profile-list")
     */
    public function list() : Response
    {
        return $this->render('Profile/list.html.twig');
    }

    /**
     * @Route("/profile/membership", name="profile-membership")
     */
    public function membership() : Response
    {
        return $this->render('Profile/membership.html.twig');
    }
}
