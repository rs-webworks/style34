<?php

namespace Style34\Controller\Profile;

use Doctrine\ORM\EntityManagerInterface;
use Style34\Entity\Profile\Profile;
use Style34\Entity\Profile\Role;
use Style34\Form\Profile\RegistrationForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class RegistrationController
 * @package Style34\Controller\Profile
 */
class RegistrationController extends AbstractController
{

    /**
     * @Route("/profile/registration", name="profile-registration")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function index(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    ) {

        $profile = new Profile();
        $form = $this->createForm(RegistrationForm::class, $profile);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // Get default profile role
            $role = $em->getRepository(Role::class)->findOneBy(array('name' => Role::INACTIVE));
            $profile->setRole($role);

            // Encode password
            $password = $passwordEncoder->encodePassword($profile, $profile->getPlainPassword());
            $profile->setPassword($password);

            // Save entity
            $profile->setCreatedAt(new \DateTime());
            $em->persist($profile);
            $em->flush();

            $this->addFlash('success', $translator->trans('registration-success', [], 'profile'));
        }

        return $this->render(
            'Profile/Registration/index.html.twig',
            array('form' => $form->createView())
        );
    }


    /**
     * @Route("/profile/registration/membership", name="profile-registration-membership")
     */
    public function membership()
    {

        return $this->render("Profile/Registration/membership.html.twig");
    }
}