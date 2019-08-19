<?php declare(strict_types=1);

namespace EryseClient\Form\Type\User;

use EryseClient\Entity\Server\User\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RegistrationForm
 * @package EryseClient\Form
 */
class RegistrationType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'username',
            TextType::class,
            [
                "label" => 'username',
                "translation_domain" => 'profile'
            ]
        );
        $builder->add(
            'email',
            EmailType::class,
            [
                "label" => 'email',
                "translation_domain" => 'profile'
            ]
        );
        $builder->add(
            'plainPassword',
            RepeatedType::class,
            [
                'type' => PasswordType::class,
                'invalid_message' => 'password-mismatch',
                'first_options' => [
                    "label" => 'password',
                    "translation_domain" => 'profile'
                ],
                'second_options' => [
                    "label" => 'password-again',
                    "translation_domain" => 'profile'
                ]
            ]
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}
