<?php declare(strict_types=1);

namespace EryseClient\Utility;

use EryseClient\Utility\EntityManager\ClientEntityManagerTrait;
use EryseClient\Utility\EntityManager\ServerEntityManagerTrait;

/**
 * Trait EntityManagersTrait
 * @package EryseClient\Utility
 */
trait EntityManagersTrait
{

    use ClientEntityManagerTrait;
    use ServerEntityManagerTrait;

}