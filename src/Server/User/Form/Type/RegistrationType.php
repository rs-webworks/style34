<?php declare(strict_types=1);

namespace EryseClient\Server\User\Form\Type;

use EryseClient\Server\User\Entity\UserEntity;
use EryseClient\Server\User\Validator\UserValidator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RegistrationForm
 *
 */
class RegistrationType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'username',
            TextType::class,
            [
                'label' => 'username',
                'translation_domain' => 'profile'
            ]
        );
        $builder->add(
            'email',
            EmailType::class,
            [
                'label' => 'email',
                'translation_domain' => 'profile'
            ]
        );
        $builder->add(
            'plainPassword',
            RepeatedType::class,
            [
                'type' => PasswordType::class,
                'invalid_message' => 'password-mismatch',
                'first_options' => [
                    'label' => 'password',
                    'translation_domain' => 'profile'
                ],
                'second_options' => [
                    'label' => 'password-again',
                    'translation_domain' => 'profile'
                ]
            ]
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => UserValidator::class,
            ]
        );
    }
}
