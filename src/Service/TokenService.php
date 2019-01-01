<?php

namespace EryseClient\Service;

use EryseClient\Entity\User\User;
use EryseClient\Entity\Token\Token;
use EryseClient\Entity\Token\TokenType;
use EryseClient\Repository\Token\TokenRepository;
use EryseClient\Repository\Token\TokenTypeRepository;
use EryseClient\Traits\EntityManagerTrait;

/**
 * Class TokenService
 * @package EryseClient\Service
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
     * @param User $user
     * @return Token
     * @throws \Exception
     */
    public function getActivationToken(User $user): Token
    {
        $token = new Token();
        $token->setHash($this->generateHash());
        $token->setUser($user);
        $createdAt = new \DateTime();
        $expiresAt = new \DateTime();
        $token->setCreatedAt($createdAt);
        $token->setExpiresAt($this->createExpirationDateTime($expiresAt, Token::EXPIRY_HOUR * 2));
        $token->setType($this->tokenTypeRepository->findOneBy(array('name' => TokenType::USER['ACTIVATION'])));

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
     * @param User $user
     * @return Token
     * @throws \Exception
     */
    public function getResetPasswordToken(User $user): Token
    {
        $createdAt = new \DateTime();
        $expiresAt = new \DateTime();

        $token = new Token();
        $token->setHash($this->generateHash());
        $token->setUser($user);
        $token->setCreatedAt($createdAt);
        $token->setExpiresAt($this->createExpirationDateTime($expiresAt, Token::EXPIRY_HOUR * 2));
        $token->setType($this->tokenTypeRepository->findOneBy(array(
            'name' => TokenType::USER['REQUEST_RESET_PASSWORD']
        )));

        return $token;
    }

    /**
     * Check whether the user has currently token of specified type and it is valid & un-expired
     * @param User $user
     * @param TokenType $tokenType
     * @return bool If Token of TokenType is found, is valid and is not expired
     * @throws \Exception
     */
    public function hasUserActiveTokenType(User $user, TokenType $tokenType): bool
    {
        $result = count($this->tokenRepository->findUserValidTokensOfType($user, $tokenType));

        return ($result ? true : false);
    }
}