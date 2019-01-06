<?php

namespace EryseClient\Controller\Administration;

use EryseClient\Traits\EntityManagerTrait;
use EryseClient\Traits\LoggerTrait;
use EryseClient\Traits\TranslatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApplicationController
 * @package EryseClient\Controller\Administration\Application
 * @IsGranted(EryseClient\Entity\User\Role::ADMIN)
 */
class ApplicationController extends AbstractController
{
    use TranslatorTrait;
    use LoggerTrait;
    use EntityManagerTrait;

    /**
     * @Route("/administration/serverConfiguration", name="administration-server-configuration")
     */
    public function serverConfiguration(){

    }

}