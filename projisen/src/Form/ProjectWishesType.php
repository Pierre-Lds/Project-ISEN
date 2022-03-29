<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\ProjectWishes;
use App\Entity\Student;
use App\Repository\ProjectRepository;
use App\Repository\StudentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ProjectWishesType extends AbstractType {
    private $projectRepository;
    private $security;
    private $studentRepository;

    public function __construct(Security $security, ProjectRepository $projectRepository, StudentRepository $studentRepository) {
        $this->projectRepository = $projectRepository;
        $this->security = $security;
        $this->studentRepository = $studentRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('id_project_1', EntityType::class, ['class' => Project::class, 'placeholder' => 'Pick a project', 'choices' => $this->projectRepository->findUnattributedProjects()])
            ->add('id_project_2', EntityType::class, ['class' => Project::class, 'placeholder' => 'Pick a project', 'choices' => $this->projectRepository->findUnattributedProjects()])
            ->add('id_project_3', EntityType::class, ['class' => Project::class, 'placeholder' => 'Pick a project', 'choices' => $this->projectRepository->findUnattributedProjects()])
            ->add('id_main_student', EntityType::class, ['class' => Student::class, 'choices'  => $this->studentRepository->findMainStudentsById($this->security->getUser()->getId())])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectWishes::class,
            'translation_domain' => 'projectWishesForms'
        ]);
    }
}
