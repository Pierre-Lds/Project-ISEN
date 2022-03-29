<?php

namespace App\Controller\Security;

use App\Entity\Staff;
use App\Entity\Student;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController {
    // Init
    private $em;
    public function __construct(ManagerRegistry $em) {
        $this->em = $em->getManager();
    }
    // Authentication
    /**
     * @Route("/login",name="app.login")
     */
    public function login (AuthenticationUtils $authenticationUtils) : Response {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('Security/login.html.twig',['error'=>$error,'lastUsername'=>$lastUsername]);
    }
    /**
     * @Route("/logout-redirect",name="app.logout")
     */
    public function logoutRedirect() : Response {
        return $this->render('Security/logout.html.twig');
    }

    /**
     * @Route("/logout",name="logout")
     */
    public function logoutView () : Response {
        return $this->render('Security/logout.html.twig');
    }
}
