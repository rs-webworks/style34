<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Form\Type;

use EryseClient\Client\Profile\Role\Entity\RoleEntity;
use EryseClient\Server\UserRole\Entity\UserRole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class UserSearchType
 *
 */
class ProfileSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET');

        $builder->add(
            'id',
            TextType::class,
            [
                "required" => false,
                "label" => 'profile-id',
                "translation_domain" => 'administration'
            ]
        );

        $builder->add(
            "role",
            EntityType::class,
            [
                "class" => RoleEntity::class,
                "choice_label" => "name",
                "choice_name" => "name",
                "choice_value" => "name",
                "required" => false,
                "empty_data" => null,
                "label" => "profile-role",
                "translation_domain" => "administration"
            ]
        );
    }
}
