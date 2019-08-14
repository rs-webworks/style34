<?php declare(strict_types=1);

namespace EryseClient\Form\Administration\User;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserSearchForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET');

        $builder->add(
            'username',
            TextType::class,
            array(
                "required" => false,
                "label" => 'user-username',
                "translation_domain" => 'administration'
            )
        );

        $builder->add(
            'email',
            TextType::class,
            array(
                "required" => false,
                "label" => 'user-email',
                "translation_domain" => 'administration'
            )
        );
    }
}