<?php

namespace EryseClient\Form\User;

use EryseClient\Entity\User\User;
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
class RegistrationForm extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class, array(
            "label" => 'username',
            "translation_domain" => 'profile'
        ));
        $builder->add('email', EmailType::class, array(
            "label" => 'email',
            "translation_domain" => 'profile'
        ));
        $builder->add('plainPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'invalid_message' => 'password-mismatch',
            'first_options' => array(
                "label" => 'password',
                "translation_domain" => 'profile'
            ),
            'second_options' => array(
                "label" => 'password-again',
                "translation_domain" => 'profile'
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}