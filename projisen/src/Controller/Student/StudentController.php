<?php

namespace App\Controller\Student;

use App\Entity\Project;
use App\Entity\ProjectWishes;
use App\Entity\Student;
use App\Form\PairFormType;
use App\Form\ProjectWishesType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController {
    // Init
    private $em;
    public function __construct(ManagerRegistry $em) {
        $this->em = $em->getManager();
    }
    // General
    /**
     * @return Response
     * @Route("/student/",name="app.student.dashboard")
     */
    public function studentDashboard() : Response {
        return $this->render('Student/studentDashboard.html.twig');
    }
    // Projects (CRUD)
    /**
     * @return Response
     * @Route("/student/read-projects",name="app.student.projectsRead")
     */
    public function studentReadProjects() : Response {
        $projects = $this->em->getRepository(Project::class)->findAll();
        return $this->render('Student/studentReadProjects.html.twig',['projects' => $projects]);
    }
    /**
     * @return Response
     * @Route("/student/read-project/{id}",name="app.student.projectRead")
     */
    public function studentReadProject(Project $project) : Response {
        return $this->render('Student/studentReadProject.html.twig', ['project' => $project]);
    }
    // Projects (functions)
    /**
     * @return Response
     * @Route("/student/choose-projects",name="app.student.projectsChoose")
     */
    public function studentChooseProjects(Request $request) : Response {
        $user = $this->getUser();
        $projectWishesForm = $this->createForm(projectWishesType::class);
        $projectWishesForm->handleRequest($request);
        if ($projectWishesForm->isSubmitted() && $projectWishesForm->isValid()) {
            $wishesData = $projectWishesForm->getData();
            if ($wishesData->getIdProject1() != $wishesData->getIdProject2() and $wishesData->getIdProject1() != $wishesData->getIdProject3() and $wishesData->getIdProject2() != $wishesData->getIdProject3()) {
                $wishes = $this->em->getRepository(ProjectWishes::class)->findByStudentId($wishesData->getIdMainStudent());
                if(!$wishes) {
                    $wishes = new ProjectWishes();
                    $wishes->setIdMainStudent($wishesData->getIdMainStudent());
                    $wishes->setIdProject1($wishesData->getIdProject1());
                    $wishes->setIdProject2($wishesData->getIdProject2());
                    $wishes->setIdProject3($wishesData->getIdProject3());
                    $this->em->persist($wishes);
                } else {
                    $wishes[0]->setIdMainStudent($wishesData->getIdMainStudent());
                    $wishes[0]->setIdProject1($wishesData->getIdProject1());
                    $wishes[0]->setIdProject2($wishesData->getIdProject2());
                    $wishes[0]->setIdProject3($wishesData->getIdProject3());
                }
                $this->em->flush();
                $this->addFlash('success','Vos choix ont bien été mis à jour.');
            } else {
                $this->addFlash('error','Veuillez renseignez trois projets différents.');
            }
        }
        return $this->render('Student/studentChooseProjects.html.twig',['projectWishesForm' => $projectWishesForm->createView(),'user' => $user]);
    }
    /**
     * @return Response
     * @Route("/student/read-project-wishes",name="app.student.projectsReadWishes")
     */
    public function studentReadProjectWishes() : Response {
        $wishes = $this->em->getRepository(ProjectWishes::class)->findAll();
        return $this->render('Student/studentReadProjectWishes.html.twig',['wishes'=>$wishes]);
    }
    /**
     * @return Response
     * @Route("/student/read-attributed-projects",name="app.student.projectsReadAttributed")
     */
    public function studentReadAttributedProjects() : Response {
        $pairs = $this->em->getRepository(Student::class)->findMainStudentsWithProject();
        return $this->render('Student/studentReadAttributedProjects.html.twig',['pairs'=>$pairs]);
    }
    // Pairs (CRUD)
    /**
     * @return Response
     * @Route("/student/create-pair",name="app.student.pairCreate")
     */
    public function studentCreatePair(Request $request) : Response {
        $pairForm = $this->createForm(pairFormType::class);
        $pairForm->handleRequest($request);
        if ($pairForm->isSubmitted() && $pairForm->isValid()) {
            $idMainStudent = $pairForm->getData()['main_student']->getId();
            $idSecondStudent = $pairForm->getData()['second_student']->getId();
            $mainStudent = $this->em->getRepository(Student::class)->find($idMainStudent);
            $secondStudent = $this->em->getRepository(Student::class)->find($idSecondStudent);
            if ($mainStudent->getIdPair() == null && $secondStudent->getIdPair() == null) {
                $mainStudent->setIsMainStudent(true);
                $mainStudent->setIdPair($secondStudent);
                $secondStudent->setIdPair($mainStudent);
                $this->em->flush();
            }
            return $this->redirectToRoute('app.student.pairsRead');
        }
        return $this->render('Student/studentCreatePair.html.twig',['pairForm'=>$pairForm->createView()]);
    }
    /**
     * @return Response
     * @Route("/student/read-pairs",name="app.student.pairsRead")
     */
    public function studentReadPairs() : Response {
        $students = $this->em->getRepository(Student::class)->findAll();
        return $this->render('Student/studentReadPairs.html.twig',['students'=>$students]);
    }
    /**
     * @return Response
     * @Route("/student/delete-pair/{id}",name="app.student.pairDelete")
     */
    public function studentDeletePair(Student $student, Request $request) : Response {
        if ($this->isCsrfTokenValid('delete'.$student->getId(), $request->get('_token'))) {
            $student->setIsMainStudent(false);
            $secondStudent = $this->em->getRepository(Student::class)->find($student->getIdPair());
            $student->setIdPair(null);
            $secondStudent->setIdPair(null);
            $this->em->flush();
            $this->addFlash('success','Le binôme a été supprimé avec succès.');
        } else {
            $this->addFlash('error','Une erreur est survenue dans la suppression du binôme.');
        }
        return $this->redirectToRoute('app.student.pairsRead');
    }
    // Pairs (function)
    /**
     * @return Response
     * @Route("/student/read-pairs-without-project",name="app.student.pairsReadWithoutProject")
     */
    public function studentReadPairsWithoutProject() : Response {
        $pairs = $this->em->getRepository(Student::class)->findMainStudentsWithoutProject();
        return $this->render('Student/studentReadPairsWithoutProject.html.twig',['pairs'=>$pairs]);
    }
}
