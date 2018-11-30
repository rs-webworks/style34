<?php

namespace Style34\Controller\Profile;

use Doctrine\ORM\EntityManagerInterface;
use Style34\Entity\Profile\Profile;
use Style34\Form\Profile\RegistrationForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em)
    {

        $profile = new Profile();
        $form = $this->createForm(RegistrationForm::class, $profile);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password = $passwordEncoder->encodePassword($profile, $profile->getPlainPassword());
            $profile->setPassword($password);

            $em->persist($profile);
            $em->flush();

            $this->addFlash('success', 'Registrace byla úspěšně dokončena!');

            return $this->redirectToRoute('profile-registration-membership');
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