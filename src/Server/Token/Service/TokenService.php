<?php declare(strict_types=1);

namespace EryseClient\Server\Token\Service;

use DateInterval;
use DateTime;
use EryseClient\Common\Service\AbstractService;
use EryseClient\Common\Token\TokenInterface;
use EryseClient\Server\Token\Entity\TokenEntity;
use EryseClient\Server\Token\Exception\ExpiredTokenException;
use EryseClient\Server\Token\Exception\InvalidTokenException;
use EryseClient\Server\Token\Exception\TokenException;
use EryseClient\Server\Token\Repository\TokenRepository;
use EryseClient\Server\Token\Type\Entity\TypeEntity;
use EryseClient\Server\Token\Type\Repository\TypeRepository;
use EryseClient\Server\User\Entity\UserEntity;
use Exception;

/**
 * Class TokenService
 *
 *
 */
class TokenService extends AbstractService
{
    /** @var TypeRepository $tokenTypeRepository */
    private TypeRepository $tokenTypeRepository;

    /** @var TokenRepository $tokenRepository */
    private TokenRepository $tokenRepository;

    /**
     * TokenService constructor.
     *
     * @param TypeRepository $tokenTypeRepository
     * @param TokenRepository $tokenRepository
     */
    public function __construct(TypeRepository $tokenTypeRepository, TokenRepository $tokenRepository)
    {
        $this->tokenTypeRepository = $tokenTypeRepository;
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * @param TokenEntity $token
     *
     * @return bool
     * @throws Exception
     */
    public function isExpired(TokenEntity $token): bool
    {
        $now = new DateTime();

        return $token->getExpiresAt() < $now;
    }

    /**
     * @param TokenEntity $token
     *
     * @return bool
     */
    public function isValid(TokenEntity $token): bool
    {
        return !$token->isInvalid();
    }

    /**
     * @param TokenEntity $token
     *
     * @return TokenEntity
     */
    public function invalidate(TokenEntity $token): TokenEntity
    {
        $token->setInvalid(true);

        return $token;
    }

    /**
     * @param UserEntity $user
     *
     * @return TokenEntity
     * @throws Exception
     */
    public function getActivationToken(UserEntity $user): TokenEntity
    {
        $token = new TokenEntity();
        $token->setHash($this->generateHash());
        $token->setUser($user);
        $createdAt = new DateTime();
        $expiresAt = new DateTime();
        $token->setCreatedAt($createdAt);
        $token->setExpiresAt($this->createExpirationDateTime($expiresAt, TokenInterface::EXPIRY_HOUR * 2));
        $token->setType($this->tokenTypeRepository->findOneBy(['name' => TypeEntity::USER['ACTIVATION']]));

        return $token;
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function generateHash(): string
    {
        return sha1(random_bytes(10));
    }

    /**
     * @param DateTime $start
     * @param int $expiryInSeconds
     *
     * @return DateTime
     * @throws Exception
     */
    public function createExpirationDateTime(DateTime $start, int $expiryInSeconds): DateTime
    {
        return $start->add(new DateInterval('PT' . $expiryInSeconds . 'S'));
    }

    /**
     * @param UserEntity $user
     *
     * @return TokenEntity
     * @throws Exception
     */
    public function generateResetPasswordToken(UserEntity $user): TokenEntity
    {
        $createdAt = new DateTime();
        $expiresAt = new DateTime();

        $token = new TokenEntity();
        $token->setHash($this->generateHash());
        $token->setUser($user);
        $token->setCreatedAt($createdAt);
        $token->setExpiresAt($this->createExpirationDateTime($expiresAt, TokenInterface::EXPIRY_HOUR * 2));
        $token->setType(
            $this->tokenTypeRepository->findOneBy(['name' => TypeEntity::USER['REQUEST_RESET_PASSWORD']])
        );

        return $token;
    }

    /**
     * Check whether the user has currently token of specified type and it is valid & un-expired
     *
     * @param UserEntity $user
     * @param TypeEntity $tokenType
     *
     * @return bool If Token of TokenType is found, is valid and is not expired
     * @throws Exception
     */
    public function hasUserActiveTokenType(UserEntity $user, TypeEntity $tokenType): bool
    {
        $result = count($this->tokenRepository->findUserValidTokensOfType($user, $tokenType));

        return ($result ? true : false);
    }

    /**
     * @param TokenEntity $token
     * @param string $tokenType
     *
     * @throws ExpiredTokenException
     * @throws InvalidTokenException
     * @throws TokenException
     */
    public function verifyToken(TokenEntity $token, string $tokenType): void
    {
        if (!$token) {
            throw new TokenException('token: '. $token->getHash()); //todo: add exception for missing token
        }

        if ($token->getType() !== $tokenType) {
            throw new TokenException('token: '. $token->getHash()); //todo: add exception for incorrect token type
        }

        if (!$this->isValid($token)) {
            throw new InvalidTokenException('token: '. $token->getHash());
        }

        if ($this->isExpired($token)) {
            throw new ExpiredTokenException('token: '. $token->getHash());
        }
    }
}
