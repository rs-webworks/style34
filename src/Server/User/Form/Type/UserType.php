<?php declare(strict_types=1);

namespace EryseClient\Server\User\Form\Type;

use EryseClient\Client\Profile\Role\Repository\RoleRepository;
use EryseClient\Server\User\Role\Entity\RoleEntity;
use EryseClient\Server\User\Entity\UserEntity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserForm
 *
 */
class UserType extends AbstractType
{

    /** @var RoleRepository */
    private $roleRepository;

    /**
     * UserForm constructor.
     *
     * @param RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'id',
            TextType::class,
            [
                "label" => 'id',
                "translation_domain" => 'global'
            ]
        );

        $builder->add(
            'username',
            TextType::class,
            [
                "label" => 'user-username',
                "translation_domain" => 'administration'
            ]
        );

        $builder->add(
            'email',
            EmailType::class,
            [
                "label" => 'user-email',
                "translation_domain" => 'administration'
            ]
        );

        $builder->add(
            'createdAt',
            DateTimeType::class,
            [
                "label" => "created-at",
                "translation_domain" => 'administration'
            ]
        );

        $builder->add(
            'activatedAt',
            DateTimeType::class,
            [
                "label" => "activated-at",
                "translation_domain" => 'administration',
            ]
        );

        $builder->add(
            'roleEntity',
            EntityType::class,
            [
                "label" => "user-role",
                "class" => RoleEntity::class,
                "choice_label" => "name",
                "choice_name" => "name",
                "choice_value" => "name",
                "choice_translation_domain" => "global",
                "translation_domain" => 'administration',
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
                'data_class' => UserEntity::class,
            ]
        );
    }
}
