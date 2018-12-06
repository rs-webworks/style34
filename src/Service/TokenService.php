<?php

namespace Style34\Service;

use Style34\Entity\Token\Token;

/**
 * Class TokenService
 * @package Style34\Service
 */
class TokenService extends AbstractService
{

    /**
     * @return string
     * @throws \Exception
     */
    public function generateActivationToken()
    {
        return $this->generateHash();
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function generateHash()
    {
        return sha1(random_bytes(10));
    }

    /**
     * @param Token $token
     * @return bool
     * @throws \Exception
     */
    public function isValid(Token $token)
    {
        $now = new \DateTime();
        return $token->getExpiresAt() <= $now;
    }
}