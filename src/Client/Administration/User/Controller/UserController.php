<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\User\Controller;

use EryseClient\Client\Profile\Entity\Entity\Role;
use EryseClient\Common\Controller\ControllerSettings;
use EryseClient\Server\User\Form\Type\UserSearchType;
use EryseClient\Server\User\Form\Type\UserType;
use EryseClient\Server\User\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RoleController
 * @package EryseClient\Controller\Administration\User
 * @IsGranted(EryseClient\Server\UserRole\Entity\UserRole::ADMIN)
 */
class UserController extends AbstractController
{

    /**
     * @Route("/administration/users",name="administration-users-index")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator)
    {
        $searchForm = $this->createForm(UserSearchType::class);
        $qb = $userRepository->createQueryBuilder('u')
            ->orderBy("u.id");
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            foreach ($searchForm->getData() as $column => $value) {
                if ($value === null) {
                    continue;
                }

                if ($column == "roleEntity") {
                    /** @var Role $value */
                    $qb->andWhere("u.role = :role");
                    $qb->setParameter("role", $value->getName());
                    continue;
                }

                $qb->andWhere('u.' . $column . ' LIKE :val');
                $qb->setParameter('val', '%' . $value . '%');
            }
        }

        $users = $paginator->paginate(
            $qb,
            $request->query->get("page") ? (int) $request->query->get("page") : 1,
            ControllerSettings::PAGINATOR_DEFAULT_IPP
        );

        return $this->render(
            'Administration/User/User/index.html.twig',
            ["users" => $users, "searchForm" => $searchForm->createView()]
        );
    }

    /**
     * @Route("/administration/user/{id}/{username}", name="administration-user-view")
     * @param $id
     * @param UserRepository $userRepository
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
