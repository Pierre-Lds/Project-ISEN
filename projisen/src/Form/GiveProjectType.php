<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Student;
use App\Repository\ProjectRepository;
use App\Repository\StudentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GiveProjectType extends AbstractType {
    private $studentRepository;
    private $projectRepository;
    public function __construct(StudentRepository $studentRepository, ProjectRepository $projectRepository) {
        $this->studentRepository = $studentRepository;
        $this->projectRepository = $projectRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('main_student', EntityType::class, ['class' => Student::class, 'placeholder' => 'Pick a student', 'choices' => $this->studentRepository->findMainStudentsWithoutProject()])
            ->add('project', EntityType::class, ['class' => Project::class, 'placeholder' => 'Pick a project', 'choices' => $this->projectRepository->findUnattributedProjects()])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'translation_domain' => 'giveProjectForms'
        ]);
    }
}
