<?php

namespace eRyseClient\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LoginForm
 * @package eRyseClient\Form\Profile
 */
class LoginForm extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('username', TextType::class, array(
            "label" => 'username-or-mail',
            "translation_domain" => 'security'
        ));

        $builder->add('password', PasswordType::class, array(
            'label' => 'password',
            'translation_domain' => 'security'
        ));

        $builder->add('remember-me', CheckboxType::class, array(
           'label' => 'remember-me',
           'translation_domain' => 'security'
        ));
    }

}