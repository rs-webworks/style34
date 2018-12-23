<?php

namespace Style34\Service;

use Style34\Entity\Profile\Profile;
use Style34\Entity\Token\Token;
use Style34\Entity\Token\TokenType;
use Style34\Repository\Token\TokenRepository;
use Style34\Repository\Token\TokenTypeRepository;
use Style34\Traits\EntityManagerTrait;

/**
 * Class TokenService
 * @package Style34\Service
 */
class TokenService extends AbstractService
{
    use EntityManagerTrait;

    /** @var TokenTypeRepository $tokenTypeRepository */
    private $tokenTypeRepository;

    /** @var TokenRepository $tokenRepository */
    private $tokenRepository;

    /**
     * TokenService constructor.
     * @param TokenTypeRepository $tokenTypeRepository
     * @param TokenRepository $tokenRepository
     */
    public function __construct(TokenTypeRepository $tokenTypeRepository, TokenRepository $tokenRepository)
    {
        $this->tokenTypeRepository = $tokenTypeRepository;
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * @param Token $token
     * @return bool
     * @throws \Exception
     */
    public function isExpired(Token $token): bool
    {
        $now = new \DateTime();

        return $token->getExpiresAt() < $now;
    }

    /**
     * @param Token $token
     * @return bool
     */
    public function isValid(Token $token): bool
    {
        return !$token->isInvalid();
    }

    /**
     * @param Profile $profile
     * @return Token
     * @throws \Exception
     */
    public function getActivationToken(Profile $profile): Token
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

    /**
     * @return string
     * @throws \Exception
     */
    protected function generateHash(): string
    {
        return sha1(random_bytes(10));
    }

    /**
     * @param \DateTime $start
     * @param int $expiryInSeconds
     * @return \DateTime
     * @throws \Exception
     */
    public function createExpirationDateTime(\DateTime $start, int $expiryInSeconds): \DateTime
    {
        return $start->add(new \DateInterval('PT' . $expiryInSeconds . 'S'));
    }

    /**
     * @param Profile $profile
     * @return Token
     * @throws \Exception
     */
    public function getResetPasswordToken(Profile $profile): Token
    {
        $createdAt = new \DateTime();
        $expiresAt = new \DateTime();

        $token = new Token();
        $token->setHash($this->generateHash());
        $token->setProfile($profile);
        $token->setCreatedAt($createdAt);
        $token->setExpiresAt($this->createExpirationDateTime($expiresAt, Token::EXPIRY_HOUR * 2));
        $token->setType($this->tokenTypeRepository->findOneBy(array(
            'name' => TokenType::PROFILE['REQUEST_RESET_PASSWORD']
        )));

        return $token;
    }

    /**
     * Check whether the profile has currently token of specified type and it is valid & un-expired
     * @param Profile $profile
     * @param TokenType $tokenType
     * @return bool If Token of TokenType is found, is valid and is not expired
     * @throws \Exception
     */
    public function hasProfileActiveTokenType(Profile $profile, TokenType $tokenType): bool
    {
        $result = count($this->tokenRepository->findProfileValidTokensOfType($profile, $tokenType));

        return ($result ? true : false);
    }
}