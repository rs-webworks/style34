<?php declare(strict_types=1);

namespace EryseClient\Component\Server\User\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LoginType
 * @package EryseClient\Form\Type\Security
 */
class LoginType extends AbstractType
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
                "label" => 'username-or-mail',
                "translation_domain" => 'security'
            ]
        );

        $builder->add(
            'password',
            PasswordType::class,
            [
                'label' => 'password',
                'translation_domain' => 'security'
            ]
        );

        $builder->add(
            'remember-me',
            CheckboxType::class,
            [
                'label' => 'remember-me',
                'translation_domain' => 'security'
            ]
        );
    }
}
