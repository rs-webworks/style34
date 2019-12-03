<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Facade;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Client\Profile\Repository\ProfileRepository;
use EryseClient\Client\Profile\Service\ProfileService;
use EryseClient\Client\ProfileRole\Entity\ProfileRole;
use EryseClient\Client\ProfileRole\Repository\ProfileRoleRepository;
use EryseClient\Client\ProfileRole\Service\ProfileRoleService;
use EryseClient\Common\Controller\ControllerSettings;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class ProfileFacade
 *
 * @package EryseClient\Client\Profile\Facade
 */
class ProfileFacade
{
    /** @var ProfileService */
    private $profileService;

    /** @var ProfileRepository */
    protected $profileRepository;

    /** @var ProfileRoleService */
    private $profileRoleService;

    /** @var ProfileRoleRepository */
    protected $profileRoleRepository;

    /** @var PaginatorInterface */
    private $paginator;

    /**
     * ProfileFacade constructor.
     *
     * @param ProfileService $profileService
     * @param ProfileRepository $profileRepository
     * @param ProfileRoleService $profileRoleService
     * @param ProfileRoleRepository $profileRoleRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        ProfileService $profileService,
        ProfileRepository $profileRepository,
        ProfileRoleService $profileRoleService,
        ProfileRoleRepository $profileRoleRepository,
        PaginatorInterface $paginator
    ) {
        $this->profileService = $profileService;
        $this->profileRepository = $profileRepository;
        $this->profileRoleService = $profileRoleService;
        $this->profileRoleRepository = $profileRoleRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param FormInterface $searchForm
     * @param int $page
     * @param string|null $role
     * @param bool|null $displayHidden
     *
     * @return mixed
     */
    public function getProfilesPaginated(
        FormInterface $searchForm,
        ?int $page = 1,
        ?string $role = null,
        ?bool $displayHidden = false
    ) {
        $qb = $this->profileRepository->createQueryBuilder('p')->orderBy("p.id");

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            foreach ($searchForm->getData() as $column => $value) {
                if ($value === null) {
                    continue;
                }

                if ($column == "role") {
                    /** @var ProfileRole $value */
                    $qb->andWhere("p.role = :role");
                    $qb->setParameter("role", $value);
                    continue;
                }

                if ($column == "id") {
                    $qb->andWhere('p.id = :val');
                    $qb->setParameter('val', (int) $value);
                    continue;
                }

                $qb->andWhere('p.' . $column . ' LIKE :val');
                $qb->setParameter('val', '%' . $value . '%');
            }
        } else {
            if ($role) {
                $role = $this->profileRoleRepository->findOneByName($role);
                $qb->andWhere("p.role = :role");
                $qb->setParameter("role", $role);
            } elseif (!$displayHidden) {
                $qb->andWhere("p.role IN (:defaultRoles)");
                $qb->setParameter(
                    "defaultRoles",
                    $this->profileRoleRepository->findByName($this->profileRoleService->getAllowedRolesList())
                );
            }

        }

        return $this->paginator->paginate(
            $qb,
            $page ?? 1,
            ControllerSettings::PAGINATOR_DEFAULT_IPP
        );
    }

    /**
     * @param FormInterface $profileForm
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveProfile(FormInterface $profileForm)
    {
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $profile = $profileForm->getData();
            $this->profileRepository->save($profile);
        }
    }
}
