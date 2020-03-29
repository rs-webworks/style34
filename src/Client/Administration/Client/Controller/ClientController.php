<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Client\Controller;

use EryseClient\Client\Administration\Controller\AbstractAdminController;
use EryseClient\Common\Utility\LoggerAwareTrait;
use EryseClient\Common\Utility\TranslatorAwareTrait;

/**
 * Class ClientController
 */
class ClientController extends AbstractAdminController
{
    use TranslatorAwareTrait;
    use LoggerAwareTrait;
}
