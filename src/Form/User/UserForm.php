<?php declare(strict_types=1);

namespace EryseClient\Form\User;

use DateTime;
use EryseClient\Entity\Server\User\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
            TextType::class,
            [
                "label" => "created-at",
                "translation_domain" => 'administration'
            ]
        );

        $builder->add(
            'activatedAt',
            TextType::class,
            [
                "label" => "activated-at",
                "translation_domain" => 'administration',
            ]
        );

        $builder->add(
            'roles',
            ChoiceType::class,
            [
                "label" => "user-roles",
                "translation_domain" => 'administration',
            ]
        );

        $builder->get('createdAt')
            ->addModelTransformer(
                new CallbackTransformer(
                    function ($dateTime) {
                        /** @var DateTime $dateTime */
                        return $dateTime ? $dateTime->format('Y-m-d H:i:s') : null;
                    }, function ($string) {
                    return new DateTime($string);
                }
                )
            );

        $builder->get('activatedAt')
            ->addModelTransformer(
                new CallbackTransformer(
                    function ($dateTime) {
                        /** @var DateTime $dateTime */
                        return $dateTime ? $dateTime->format('Y-m-d H:i:s') : null;
                    }, function ($string) {
                    return new DateTime($string);
                }
                )
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