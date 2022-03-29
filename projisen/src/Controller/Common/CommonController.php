<?php

namespace App\Controller\Common;

use App\Entity\Staff;
use App\Entity\Student;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CommonController extends AbstractController {
    // Init
    private $em;
    public function __construct(ManagerRegistry $em) {
        $this->em = $em->getManager();
    }
    // General
    /**
     * @return Response
     * @Route("/profile",name="app.profile")
     */
    public function homepage (Request $request, UserPasswordHasherInterface $passwordHasher) : Response {
        if($request->isMethod('post')) {
            if ($this->getUser()->getRoles()[0] == "ROLE_STUDENT") {
                $user= $this->em->getRepository(Student::class)->findOneBy(['username'=>$this->getUser()->getUsername()]);
            } else {
                $user = $this->em->getRepository(Staff::class)->findOneBy(['username'=>$this->getUser()->getUsername()]);
            }
            $user->setPassword($passwordHasher->hashPassword($user,$request->request->get('_password')));
            $this->em->flush();
            return $this->redirectToRoute('app.profile');
        }
        return $this->render('Common/profile.html.twig');
    }
}
