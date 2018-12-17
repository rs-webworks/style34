<?php

namespace Style34\Service;

use Style34\Entity\Profile\Profile;
use Style34\Entity\Token\Token;
use Style34\Entity\Token\TokenType;
use Style34\Repository\Token\TokenTypeRepository;
use Style34\Traits\EntityManagerTrait;

/**
 * Class TokenService
 * @package Style34\Service
 */
class TokenService extends AbstractService
{
    use EntityManagerTrait;

    /**
     * @var TokenTypeRepository
     */
    private $tokenTypeRepository;

    /**
     * TokenService constructor.
     * @param TokenTypeRepository $tokenTypeRepository
     */
    public function __construct(TokenTypeRepository $tokenTypeRepository)
    {
        $this->tokenTypeRepository = $tokenTypeRepository;
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
    public function isExpired(Token $token)
    {
        $now = new \DateTime();

        return $token->getExpiresAt() < $now;
    }

    /**
     * @param Token $token
     * @return bool
     */
    public function isValid(Token $token)
    {
        return !$token->isInvalid();
    }

    /**
     * @param \DateTime $start
     * @param int $expiryInSeconds
     * @return \DateTime
     * @throws \Exception
     */
    public function createExpirationDateTime(\DateTime $start, int $expiryInSeconds)
    {
        return $start->add(new \DateInterval('PT' . $expiryInSeconds . 'S'));
    }

    /**
     * @param Profile $profile
     * @return Token
     * @throws \Exception
     */
    public function getActivationToken(Profile $profile)
    {
        $token = new Token();
        $token->setHash($this->generateHash());
        $token->setProfile($profile);
        $createdAt = new \DateTime();
        $expiresAt = new \DateTime();
        $token->setCreatedAt($createdAt);
        $token->setExpiresAt($this->createExpirationDateTime($expiresAt, Token::EXPIRY_HOUR * 2));
        $token->setType($this->tokenTypeRepository->findOneBy(array('name' => TokenType::PROFILE['ACTIVATION'])));

        return $token;
    }
}