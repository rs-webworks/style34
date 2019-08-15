<?php declare(strict_types=1);

namespace EryseClient\Form\User;

use DateTime;
use EryseClient\Entity\Client\User\Role;
use EryseClient\Entity\Server\User\User;
use EryseClient\Repository\Client\User\RoleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserForm
 * @package EryseClient\Form\User
 */
class UserForm extends AbstractType
{

    /** @var RoleRepository  */
    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => User::class,
            )
        );
    }

}