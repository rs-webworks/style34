<?php declare(strict_types=1);

namespace EryseClient\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Parent_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;

abstract class AbstractController extends SymfonyAbstractController
{

    /** @var EntityManagerInterface */
    protected $serverEm;

    /** @var EntityManagerInterface */
    protected $clientEm;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->clientEm = $managerRegistry->getManager('eryseClient');
        $this->serverEm = $managerRegistry->getManager('eryseServer');
    }


}