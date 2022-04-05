<?php

namespace App\Controller\Admin;

use App\Entity\ProfessionalDomain;
use App\Entity\ProjectWishes;
use App\Entity\Staff;
use App\Entity\Project;
use App\Entity\Student;
use App\Entity\Thematic;
use App\Form\GiveProjectType;
use App\Form\ProfessionalDomainFormType;
use App\Form\ProjectFormType;
use App\Form\StaffFormType;
use App\Form\PairFormType;
use App\Form\StudentFormType;
use App\Form\ThematicFormType;
use App\Repository\ProjectRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController {
    // Init
    private $em;
    private $projectRepository;
    public function __construct(ManagerRegistry $em, ProjectRepository $projectRepository) {
        $this->em = $em->getManager();
        $this->projectRepository = $projectRepository;
    }
    // General
    /**
     * @return Response
     * @Route("/admin/",name="app.admin.dashboard")
     */
    public function adminDashboard() : Response {
        return $this->render('Admin/adminDashboard.html.twig');
    }
    // Students (CRUD)
    /**
     * @return Response
     * @Route("/admin/create-student",name="app.admin.studentCreate")
     */
    public function adminCreateStudent(Request $request, UserPasswordHasherInterface $passwordHasher) : Response
    {
        $student = new Student();
        $studentForm = $this->createForm(studentFormType::class, $student);
        $studentForm->handleRequest($request);
        if ($studentForm->isSubmitted()) {
            $studentData = $studentForm->getData();
            echo($studentData->getUsername());
            $student->setPassword($passwordHasher->hashPassword($student, $studentData->getPassword()));
            $student->setRoles(['ROLE_STUDENT']);
            $first_names = preg_split('/[- ]+/', $studentData->getFirstName());
            $fname = "";
            foreach ($first_names as $first_name) {
                $fname .= $first_name[0];
            }
            $lname = $studentData->getLastName()."00000";
            $lname = substr($lname, 0, 5+1-sizeof($first_names));
            $student->setUsername(strtolower($fname.$lname.($studentData->getGraduationYear()%100)));
            if ($studentForm->isValid()) {
                $this->em->persist($student);
                $this->em->flush();
                $this->addFlash('success', 'L\'étudiant a été créé avec succès.');
                return $this->redirectToRoute('app.admin.studentsRead');
            }
        }
        return $this->render('Admin/adminCreateStudent.html.twig',['studentForm'=>$studentForm->createView()]);
    }

    /**
     * @return Response
     * @Route("/admin/read-students",name="app.admin.studentsRead")
     */
    public function adminReadStudents() : Response {
        $students = $this->em->getRepository(Student::class)->findAll();
        return $this->render('Admin/adminReadStudents.html.twig',['students'=>$students]);
    }
    /**
     * @return Response
     * @Route("/admin/read-student/{id}",name="app.admin.studentRead")
     */
    public function adminReadStudent(Student $student) : Response {
        return $this->render('Admin/adminReadStudent.html.twig',['student'=>$student]);
    }
    /**
     * @return Response
     * @Route("/admin/update-student/{id}",name="app.admin.studentUpdate")
     */
    public function adminUpdateStudent(Student $student, Request $request, UserPasswordHasherInterface $passwordHasher) : Response {
        $studentForm = $this->createForm(StudentFormType::class, $student);
        $studentForm->handleRequest($request);
        if ($studentForm->isSubmitted()) {
            $studentData = $studentForm->getData();
            $student->setRoles(['ROLE_STUDENT']);
            $first_names = preg_split('/[- ]+/', $studentData->getFirstName());
            $fname = "";
            foreach ($first_names as $first_name) {
                $fname .= $first_name[0];
            }
            $lname = $studentData->getLastName()."00000";
            $lname = substr($lname, 0, 5+1-sizeof($first_names));
            $student->setUsername(strtolower($fname.$lname.($studentData->getGraduationYear()%100)));
            if ($studentForm->isValid()) {
                $pwd = $student->getPassword();
                $this->em->flush();
                $student->setPassword($pwd);
                $this->addFlash('success', 'L\'étudiant a été édité avec succès.');
            } else {
                $this->addFlash('error', 'Une erreur est survenue dans l\'édition de l\'étudiant.');
            }
            return $this->redirectToRoute('app.admin.studentsRead');
        }
        return $this->render('Admin/adminUpdateStudent.html.twig',['student'=>$student,'studentForm'=>$studentForm->createView()]);
    }

    /**
     * @return Response
     * @Route("/admin/delete-student/{id}",name="app.admin.studentDelete")
     */
    public function adminDeleteStudent(Student $student, Request $request) : Response {
        if ($this->isCsrfTokenValid('delete'.$student->getId(), $request->get('_token'))) {
            $project = $student->getIdProject();
            if ($project != null) {
                $project->setIsTaken(false);
            }
            $pair = $student->getIdPair();
            if ($pair != null) {
                $pair->setIsMainStudent(false);
                $pair->setIdPair(null);
                $pair->setIdProject(null);
            }
            $student->setIdPair(null);
            $this->em->remove($student);
            $this->em->flush();
            $this->addFlash('success','L\'étudiant a été supprimé avec succès.');
        } else {
            $this->addFlash('error','Une erreur est survenue dans la suppression de l\'étudiant.');
        }
        return $this->redirectToRoute('app.admin.studentsRead');
    }
    // Teachers (CRUD)
    /**
     * @return Response
     * @Route("/admin/create-teacher",name="app.admin.teacherCreate")
     */
    public function adminCreateTeacher(Request $request, UserPasswordHasherInterface $passwordHasher) : Response {
        $staff = new Staff();
        $staffForm = $this->createForm(staffFormType::class,$staff);
        $staffForm->handleRequest($request);
        if($staffForm->isSubmitted()) {
            $staffData = $staffForm->getData();
            $staff->setPassword($passwordHasher->hashPassword($staff,$staffData->getPassword()));
            if ($staffData->getIsAdmin() == true) {
                $staff->setRoles(['ROLE_ADMIN']);
            } else {
                $staff->setRoles(['ROLE_TEACHER']);
            }
            $first_names = preg_split('/[- ]+/',$staffData->getFirstName());
            $fname = "";
            foreach ($first_names as $first_name) {
                $fname .= $first_name[0];
            }
            $staff->setUsername(strtolower($fname.$staffData->getLastName()));
            if($staffForm->isValid()) {
                $this->em->persist($staff);
                $this->em->flush();
                $this->addFlash('success','L\'enseignant a été créé avec succès.');
                return $this->redirectToRoute('app.admin.teachersRead');
            }
        }
        return $this->render('Admin/adminCreateTeacher.html.twig',['staffForm'=>$staffForm->createView()]);
    }
    /**
     * @return Response
     * @Route("/admin/read-teachers",name="app.admin.teachersRead")
     */
    public function adminReadTeachers() : Response {
        $staffs = $this->em->getRepository(Staff::class)->findAll();
        return $this->render('Admin/adminReadTeachers.html.twig',['staffs'=>$staffs]);
    }
    /**
     * @return Response
     * @Route("/admin/read-teacher/{id}",name="app.admin.teacherRead")
     */
    public function adminReadTeacher(Staff $staff) : Response {
        return $this->render('Admin/adminReadTeacher.html.twig',['staff'=>$staff]);
    }
    /**
     * @return Response
     * @Route("/admin/update-teacher/{id}",name="app.admin.teacherUpdate")
     */
    public function adminUpdateTeacher(Staff $staff, Request $request, UserPasswordHasherInterface $passwordHasher) : Response {
        $staffForm = $this->createForm(StaffFormType::class, $staff);
        $staffForm->handleRequest($request);
        if ($staffForm->isSubmitted()) {
            $staffData = $staffForm->getData();
            if ($staffData->getIsAdmin() == true) {
                $staff->setRoles(['ROLE_ADMIN']);
            } else {
                $staff->setRoles(['ROLE_TEACHER']);
            }
            $staffData['password'] = "";
            $staffForm->setData($staffData);
            if ($staffForm->isValid()) {
                $pwd = $staff->getPassword();
                $this->em->flush();
                $staff->setPassword($pwd);
                $this->addFlash('success','L\'enseignant a été édité avec succès');
                return $this->redirectToRoute('app.admin.teachersRead');
            }
        }
        return $this->render('Admin/adminUpdateTeacher.html.twig',['staff'=>$staff,'staffForm'=>$staffForm->createView()]);
    }
    /**
     * @return Response
     * @Route("/admin/delete-teacher/{id}",name="app.admin.teacherDelete")
     */
    public function adminDeleteTeacher(Staff $staff, Request $request) : Response {
        if ($this->isCsrfTokenValid('delete'.$staff->getId(), $request->get('_token'))) {
            $this->em->remove($staff);
            $this->em->flush();
            $this->addFlash('success','L\'enseignant a été supprimé avec succès.');
        } else {
            $this->addFlash('error','Une erreur est survenue dans la suppression de l\'enseignant.');
        }
        return $this->redirectToRoute('app.admin.teachersRead');
    }
    // Projects (CRUD)
    /**
     * @return Response
     * @Route("/admin/delete-project/{id}",name="app.admin.projectDelete")
     */
    public function adminDeleteProject(Project $project, Request $request) : Response {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->get('_token'))) {
            $students = $project->getStudents();
            foreach ($students as $student) {
                $student->setIdProject(null);
            }
            $this->em->remove($project);
            $this->em->flush();
            $this->addFlash('success','Le projet a été supprimé avec succès.');
        } else {
            $this->addFlash('error','Une erreur est survenue dans la suppression du projet.');
        }
        return $this->redirectToRoute('app.admin.projectsRead');
    }
    // Projects (functions)
    /**
     * @return Response
     * @Route("/admin/unattribute-project/{id}",name="app.admin.projectsUnattribute")
     */
    public function adminUnattributeProject(Student $student, Request $request) : Response {
        if ($this->isCsrfTokenValid('delete'.$student->getId(), $request->get('_token'))) {
            $secondStudent = $this->em->getRepository(Student::class)->find($student->getIdPair());
            $project = $this->em->getRepository(Project::class)->find($student->getIdProject());
            $project->setIsTaken(false);
            $student->setIdProject(null);
            $secondStudent->setIdProject(null);
            $this->em->flush();
            $this->addFlash('success','Le projet a été oté avec succès.');
        } else {
            $this->addFlash('error','Une erreur est survenue dans la suppression du binôme.');
        }
        return $this->redirectToRoute('app.teacher.projectsReadAttributed');
    }
    /**
     * @return Response
     * @Route("/admin/launch-project-attribution",name="app.admin.projectsLaunch")
     */
    public function adminLaunchProjects(Request $request) : Response {
        if ($request->isMethod('POST')) {
            exec("./scripts/algo/output/out false fromDB");
            $this->addFlash('success','L\'algorithme s\'est exécuté avec succès.');
        }
        return $this->render('Admin/adminLaunchProjects.html.twig');
    }
    /**
     * @return Response
     * @Route("/admin/give-projects",name="app.admin.projectsGive")
     */
    public function adminGiveProjects(Request $request) : Response {
        $giveProjectForm = $this->createForm(giveProjectType::class);
        $giveProjectForm->handleRequest($request);
        if ($giveProjectForm->isSubmitted() && $giveProjectForm->isValid()) {
            $projectData = $giveProjectForm->getData();
            $project = $projectData['project'];
            $project->setIsTaken(true);
            $student = $projectData['main_student'];
            $student->setIdProject($project);
            $sstudent = $student->getIdPair();
            $sstudent->setIdProject($project);
            $this->em->flush();
            return $this->redirectToRoute('app.teacher.projectsReadAttributed');
        }
        return $this->render('Admin/adminGiveProjects.html.twig',['giveProjectForm'=>$giveProjectForm->createView()]);
    }
    // Pairs (CRUD)
    /**
     * @return Response
     * @Route("/admin/create-pair",name="app.admin.pairCreate")
     */
    public function adminCreatePair(Request $request) : Response {
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
            return $this->redirectToRoute('app.admin.pairsRead');
        }
        return $this->render('Admin/adminCreatePair.html.twig',['pairForm'=>$pairForm->createView()]);
    }
    /**
     * @return Response
     * @Route("/admin/delete-pair/{id}",name="app.admin.pairDelete")
     */
    public function adminDeletePair(Student $student, Request $request) : Response {
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
        return $this->redirectToRoute('app.teacher.pairsRead');
    }
    // Thematics (CRUD)
    /**
     * @return Response
     * @Route("/admin/thematics",name="app.admin.thematics")
     */
    public function adminCreateThematic(Request $request) : Response {
        $thematics = $this->em->getRepository(Thematic::class)->findAll();
        $thematic = new Thematic();
        $thematicForm = $this->createForm(thematicFormType::class,$thematic);
        $thematicForm->handleRequest($request);
        if ($thematicForm->isSubmitted() && $thematicForm->isValid()) {
            $this->em->persist($thematic);
            $this->em->flush();
            $this->addFlash('success','La thématique a été créée avec succès.');
            return $this->redirectToRoute('app.admin.thematics');
        }
        return $this->render('Admin/adminThematics.html.twig',['thematicForm'=>$thematicForm->createView(), 'thematics'=>$thematics]);
    }
    /**
     * @return Response
     * @Route("/admin/delete-thematic/{id}",name="app.admin.thematicDelete")
     */
    public function adminDeleteThematic(Request $request, Thematic $thematic) : Response {
        if ($this->isCsrfTokenValid('delete'.$thematic->getId(), $request->get('_token'))) {
            $projects = $this->projectRepository->findByThematic($thematic->getId());
            $them = $this->em->getRepository(Thematic::class)->findByName("Aucune");
            foreach ($projects as $project) {
                $project->setIdThematic($them[0]);
            }
            $this->em->remove($thematic);
            $this->em->flush();
            $this->addFlash('success','La thématique a été supprimée avec succès.');
        } else {
            $this->addFlash('error','Une erreur est survenue dans la suppression de la thématique.');
        }
        return $this->redirectToRoute('app.admin.thematics');
    }
    // Professional Domains (CRUD)
    /**
     * @return Response
     * @Route("/admin/professional-domains",name="app.admin.professionalDomains")
     */
    public function adminProfessionalDomains(Request $request) : Response {
        $professionalDomains = $this->em->getRepository(ProfessionalDomain::class)->findAll();
        $professionalDomain = new professionalDomain();
        $professionalDomainForm = $this->createForm(professionalDomainFormType::class,$professionalDomain);
        $professionalDomainForm->handleRequest($request);
        if ($professionalDomainForm->isSubmitted() && $professionalDomainForm->isValid()) {
            $this->em->persist($professionalDomain);
            $this->em->flush();
            $this->addFlash('success','Le domaine professionnel a été ajouté avec succès.');
            return $this->redirectToRoute('app.admin.professionalDomains');
        }
        return $this->render('Admin/adminProfessionalDomains.html.twig',['professionalDomainForm'=>$professionalDomainForm->createView(), 'professionalDomains'=>$professionalDomains]);
    }
    /**
     * @return Response
     * @Route("/admin/delete-professional-domain/{id}",name="app.admin.professionalDomainDelete")
     */
    public function adminDeleteProfessionalDomain(Request $request, ProfessionalDomain $professionalDomain) : Response {
        if ($this->isCsrfTokenValid('delete'.$professionalDomain->getId(), $request->get('_token'))) {
            $this->em->remove($professionalDomain);
            $this->em->flush();
            $this->addFlash('success','Le domaine professionnel a été supprimé avec succès.');
        }
        return $this->redirectToRoute('app.admin.professionalDomains');
    }
    // CSV
    /**
     * @return Response
     * @Route("/admin/populate-with-csv",name="app.admin.csvPopulation")
     */
    public function adminCSVPopulation(Request $request, UserPasswordHasherInterface $passwordHasher) : Response {
        if ($request->isMethod('POST')) {
            exec("./scripts/csvPopulation/out populateDBWithCSV");
            exec("./scripts/csvPopulation/out populateWishes");
            $students = $this->em->getRepository(Student::class)->findAllWithPwd('test');
            $staff = $this->em->getRepository(Staff::class)->findAllWithPwd('root');
            foreach ($students as $student) {
                $student->setPassword($passwordHasher->hashPassword($student,'test'));
            }
            foreach ($staff as $staf) {
                $staf->setPassword($passwordHasher->hashPassword($staf,'root'));
            }
            $this->em->flush();
            $this->addFlash('success','L\'import de données s\'est exécuté avec succès.');
        }
        return $this->render('Admin/adminPopulateWithCSV.html.twig');
    }
    /**
     * @return Response
     * @Route("/admin/get-csv",name="app.admin.getCSV")
     */
    public function adminGetCSV(Request $request) : Response {
        if ($request->isMethod('POST')) {
            exec("./scripts/csvPopulation/out exportToCSV student");
            exec("./scripts/csvPopulation/out exportToCSV staff");
            exec("./scripts/csvPopulation/out exportToCSV thematic");
            exec("./scripts/csvPopulation/out exportToCSV project");
            exec("./scripts/csvPopulation/out exportToCSV domainePro");
            $this->addFlash('success','L\'export de données s\'est exécuté avec succès.');
        }
        return $this->render('Admin/adminGetCSV.html.twig');
    }
    /**
     * @return Response
     * @Route("/admin/delete-data",name="app.admin.deleteData")
     */
    public function adminDeleteData(Request $request, UserPasswordHasherInterface $passwordHasher) : Response {
        if ($request->isMethod('POST')) {
            exec("./scripts/csvPopulation/out deleteAll");
            $admin = new Staff();
            $admin->setIsAdmin(true);
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setPassword($passwordHasher->hashPassword($admin,"admin"));
            $admin->setFirstName("admin");
            $admin->setLastName("admin");
            $admin->setUsername("admin");
            $this->em->persist($admin);
            $thematic = new Thematic();
            $thematic->setName("Aucune");
            $this->em->persist($thematic);
            $dp = new ProfessionalDomain();
            $dp->setName("Indifférent");
            $this->em->persist($dp);
            $this->em->flush();
            return $this->redirectToRoute('app.homepage');
        }
        return $this->render('Admin/adminDeleteData.html.twig');
    }
}
