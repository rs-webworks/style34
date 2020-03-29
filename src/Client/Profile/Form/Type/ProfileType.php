<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Form\Type;

use EryseClient\Client\Profile\Entity\ProfileEntity;
use EryseClient\Client\Profile\Role\Entity\RoleEntity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProfileType
 *
 *
 */
class ProfileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'role', EntityType::class, [
            'class' => RoleEntity::class,
            'choice_label' => 'name',
            'choice_translation_domain' => 'administration'
        ]);
        $builder->add('state', TextType::class);
        $builder->add('city', TextType::class);
        $builder->add('occupation', TextType::class);

        $builder->add('save', SubmitType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ProfileEntity::class]);
    }

}
