<?php declare(strict_types=1);

namespace EryseClient\Server\User\Form\Type;

use EryseClient\Server\UserRole\Entity\UserRole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class UserSearchType
 *
 */
class UserSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET');

        $builder->add(
            'username',
            TextType::class,
            [
                "required" => false,
                "label" => 'user-username',
                "translation_domain" => 'administration'
            ]
        );

        $builder->add(
            'email',
            TextType::class,
            [
                "required" => false,
                "label" => 'user-email',
                "translation_domain" => 'administration'
            ]
        );

        $builder->add(
            "roleEntity",
            EntityType::class,
            [
                "class" => UserRole::class,
                "choice_label" => "name",
                "choice_name" => "name",
                "choice_value" => "name",
                "required" => false,
                "empty_data" => null,
                "label" => "user-role",
                "translation_domain" => "administration"
            ]
        );
    }
}
