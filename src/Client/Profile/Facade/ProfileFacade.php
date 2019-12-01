<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Facade;

use EryseClient\Client\Profile\Repository\ProfileRepository;
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
    /** @var ProfileRepository */
    protected $profileRepository;

    /** @var ProfileRoleRepository */
    protected $profileRoleRepository;

    /** @var PaginatorInterface */
    private $paginator;
    /**
     * @var ProfileRoleService
     */
    private $profileRoleService;

    /**
     * ProfileFacade constructor.
     *
     * @param ProfileRepository $profileRepository
     * @param ProfileRoleRepository $profileRoleRepository
     * @param ProfileRoleService $profileRoleService
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        ProfileRepository $profileRepository,
        ProfileRoleRepository $profileRoleRepository,
        ProfileRoleService $profileRoleService,
        PaginatorInterface $paginator
    ) {
        $this->profileRepository = $profileRepository;
        $this->profileRoleRepository = $profileRoleRepository;
        $this->profileRoleService = $profileRoleService;
        $this->paginator = $paginator;
    }

    /**
     * @param FormInterface $searchForm
     * @param int $page
     * @param string|null $role
     *
     * @return mixed
     */
    public function getProfilesPaginated(FormInterface $searchForm, ?int $page = 1, ?string $role = null)
    {
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
                    $qb->setParameter('val', $value);
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
            } else {
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
}
