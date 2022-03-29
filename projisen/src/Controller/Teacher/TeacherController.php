<?php

namespace App\Controller\Teacher;

use App\Entity\Project;
use App\Entity\ProjectWishes;
use App\Entity\Student;
use App\Form\ProjectFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeacherController extends AbstractController {
    // Init
    private $em;
    public function __construct(ManagerRegistry $em) {
        $this->em = $em->getManager();
    }
    // General
    /**
     * @return Response
     * @Route("/teacher/",name="app.teacher.dashboard")
     */
    public function teacherDashboard() : Response {
        return $this->render('Teacher/teacherDashboard.html.twig');
    }
    // Projects (CRUD)
    /**
     * @return Response
     * @Route("/teacher/create-project",name="app.teacher.projectCreate")
     */
    public function teacherCreateProject(Request $request) : Response {
        $project = new Project();
        $project->setYear(date('Y'));
        $projectForm = $this->createForm(ProjectFormType::class, $project);
        $projectForm->handleRequest($request);
        if ($projectForm->isSubmitted() && $projectForm->isValid()) {
            $this->em->persist($project);
            $this->em->flush();
            $this->addFlash('success','Le projet a été créé avec succès.');
            return $this->redirectToRoute('app.teacher.projectsRead');
        }
        return $this->render('Teacher/teacherCreateProject.html.twig',['projectForm'=>$projectForm->createView()]);
    }
    /**
     * @return Response
     * @Route("/teacher/read-projects",name="app.teacher.projectsRead")
     */
    public function teacherReadProjects() : Response {
        $projects = $this->em->getRepository(Project::class)->findAll();
        return $this->render('Teacher/teacherReadProjects.html.twig',['projects' => $projects]);
    }
    /**
     * @return Response
     * @Route("/teacher/read-project/{id}",name="app.teacher.projectRead")
     */
    public function teacherReadProject(Project $project) : Response {
        return $this->render('Teacher/teacherReadProject.html.twig', ['project' => $project]);
    }
    /**
     * @return Response
     * @Route("/teacher/update-project/{id}",name="app.teacher.projectUpdate")
     */
    public function teacherUpdateProject(Project $project, Request $request) : Response {
        $projectForm = $this->createForm(ProjectFormType::class, $project);
        $projectForm->handleRequest($request);
        if ($projectForm->isSubmitted() && $projectForm->isValid()) {
            $this->em->flush();
            $this->addFlash('success','Le projet a été édité avec succès.');
            return $this->redirectToRoute('app.teacher.projectsRead');
        }
        return $this->render('Teacher/teacherUpdateProject.html.twig', ['project' => $project, 'projectForm'=>$projectForm->createView()]);
    }
    /**
     * @return Response
     * @Route("/teacher/delete-project/{id}",name="app.teacher.projectDelete")
     */
    public function teacherDeleteProject(Project $project, Request $request) : Response {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->get('_token'))) {
            $this->em->remove($project);
            $this->em->flush();
            $this->addFlash('success','Le projet a été supprimé avec succès.');
        } else {
            $this->addFlash('error','Une erreur est survenue dans la suppression du projet.');
        }
        return $this->redirectToRoute('app.teacher.projectsRead');
    }
    // Projects (functions)
    /**
     * @return Response
     * @Route("/teacher/read-attributed-projects",name="app.teacher.projectsReadAttributed")
     */
    public function teacherReadAttributedProjects() : Response {
        $pairs = $this->em->getRepository(Student::class)->findMainStudentsWithProject();
        return $this->render('Teacher/teacherReadAttributedProjects.html.twig',['pairs'=>$pairs]);
    }
    /**
     * @return Response
     * @Route("/teacher/read-project-wishes",name="app.teacher.projectsReadWishes")
     */
    public function teacherReadProjectWishes() : Response {
        $wishes = $this->em->getRepository(ProjectWishes::class)->findAll();
        return $this->render('Teacher/teacherReadProjectWishes.html.twig',['wishes'=>$wishes]);
    }
    // Pairs (CRUD)
    /**
     * @return Response
     * @Route("/teacher/read-pairs",name="app.teacher.pairsRead")
     */
    public function teacherReadPairs() : Response {
        $students = $this->em->getRepository(Student::class)->findAll();
        return $this->render('Teacher/teacherReadPairs.html.twig',['students'=>$students]);
    }
    // Pairs (function)
    /**
     * @return Response
     * @Route("/teacher/read-pairs-without-project",name="app.teacher.pairsReadWithoutProject")
     */
    public function teacherReadPairsWithoutProject() : Response {
        $pairs = $this->em->getRepository(Student::class)->findMainStudentsWithoutProject();
        return $this->render('Teacher/teacherReadPairsWithoutProject.html.twig',['pairs'=>$pairs]);
    }
}
