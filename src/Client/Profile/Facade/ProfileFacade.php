<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Facade;

use EryseClient\Client\Profile\Repository\ProfileRepository;
use EryseClient\Client\Profile\Role\Entity\RoleEntity;
use EryseClient\Client\Profile\Role\Repository\RoleRepository;
use EryseClient\Client\Profile\Role\Service\RoleService;
use EryseClient\Common\Controller\ControllerSettings;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class ProfileFacade
 *
 *
 */
class ProfileFacade
{
    /** @var ProfileRepository */
    protected ProfileRepository $profileRepository;

    /** @var RoleService */
    private RoleService $profileRoleService;

    /** @var RoleRepository */
    protected RoleRepository $profileRoleRepository;

    /** @var PaginatorInterface */
    private PaginatorInterface $paginator;

    /**
     * ProfileFacade constructor.
     *
     * @param ProfileRepository $profileRepository
     * @param RoleService $profileRoleService
     * @param RoleRepository $profileRoleRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        ProfileRepository $profileRepository,
        RoleService $profileRoleService,
        RoleRepository $profileRoleRepository,
        PaginatorInterface $paginator
    ) {
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
        $qb = $this->profileRepository->createQueryBuilder('p')->orderBy('p.id');

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            foreach ($searchForm->getData() as $column => $value) {
                if ($value === null) {
                    continue;
                }

                if ($column === 'role') {
                    /** @var RoleEntity $value */
                    $qb->andWhere('p.role = :role');
                    $qb->setParameter('role', $value);
                    continue;
                }

                if ($column === 'id') {
                    $qb->andWhere('p.id = :val');
                    $qb->setParameter('val', (int) $value);
                    continue;
                }

                $qb->andWhere('p.' . $column . ' LIKE :val');
                $qb->setParameter('val', '%' . $value . '%');
            }
        } elseif ($role) {
            $roleEntity = $this->profileRoleRepository->findOneByName($role);
            $qb->andWhere('p.role = :role');
            $qb->setParameter('role', $roleEntity);
        } elseif (!$displayHidden) {
            $qb->andWhere('p.role IN (:defaultRoles)');
            $qb->setParameter(
                'defaultRoles',
                $this->profileRoleRepository->findByName($this->profileRoleService->getAllowedRolesList())
            );
        }

        return $this->paginator->paginate(
            $qb,
            $page ?? 1,
            ControllerSettings::PAGINATOR_DEFAULT_IPP
        );
    }
}
