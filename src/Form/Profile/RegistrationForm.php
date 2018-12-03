<?php

namespace Style34\Form\Profile;

use Style34\Entity\Profile\Profile;
use Style34\Entity\Profile\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RegistrationForm
 * @package Style34\Form
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
            'first_options' => array(
                "label" => 'password',
                "translation_domain" => 'profile'
            ),
            'second_options' => array(
                "label" => 'password-again',
                "translation_domain" => 'profile'
            )
        ));

        $builder->add('state', CountryType::class, array(
            'preferred_choices' => array('CZ', 'SK'),
            "label" => 'state',
            "translation_domain" => 'profile'
        ));

        $builder->add('city', TextType::class, array(
            "label" => 'city',
            "translation_domain" => 'profile'
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Profile::class,
        ));
    }
}