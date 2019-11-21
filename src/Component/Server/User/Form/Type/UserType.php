<?php declare(strict_types=1);

namespace EryseClient\Component\Server\User\Form\Type;

use EryseClient\Entity\Client\User\Role;
use EryseClient\Model\Client\ProfileRole\Repository\ProfileRoleRepository;
use EryseClient\Model\Server\User\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserForm
 * @package EryseClient\Form\User
 */
class UserType extends AbstractType
{

    /** @var ProfileRoleRepository */
    private $roleRepository;

    /**
     * UserForm constructor.
     * @param ProfileRoleRepository $roleRepository
     */
    public function __construct(ProfileRoleRepository $roleRepository)
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
                "class" => Role::class,
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
                'data_class' => User::class,
            ]
        );
    }
}
