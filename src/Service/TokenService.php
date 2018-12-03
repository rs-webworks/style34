<?php

namespace Style34\Service;

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
    public function generateActivationToken(){
        return $this->generateHash();
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function generateHash(){
        return sha1(random_bytes(10));
    }
}