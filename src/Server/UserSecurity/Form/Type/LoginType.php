<?php declare(strict_types=1);

namespace EryseClient\Server\UserSecurity\Form\Type;

use EryseClient\Server\UserSecurity\Authenticator\UserLoginFormAuthenticator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LoginType
 * @package EryseClient\Form\Type\Security
 */
class LoginType extends AbstractType
{
    public const METHOD = "POST";
    public const PREFIX = "login";
    public const USER_AUTH = 'userAuth';
    public const USER_PASSWORD = 'userPassword';
    public const REMEMBER_ME = 'rememberMe';
    public const BUTTON_SUBMIT = 'submit';
    public const TFA_CODE = 'tfaCode';
    public const TFA_TYPE = 'tfaType';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod(self::METHOD);
        $builder->setAction(UserLoginFormAuthenticator::ROUTE);

        $builder->add(
            self::USER_AUTH,
            TextType::class,
            [
                "label" => 'username-or-mail',
                "translation_domain" => 'security',
            ]
        );

        $builder->add(
            self::USER_PASSWORD,
            PasswordType::class,
            [
                'label' => 'password',
                'translation_domain' => 'security'
            ]
        );

        $builder->add(
            self::REMEMBER_ME,
            CheckboxType::class,
            [
                'label' => 'remember-me',
                'translation_domain' => 'security',
                'required' => false
            ]
        );

        $builder->add(
            self::BUTTON_SUBMIT,
            SubmitType::class,
            [
                'label' => 'login-button',
                'translation_domain' => 'security'
            ]
        );
    }
}
