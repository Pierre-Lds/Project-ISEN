<?php

namespace App\Form;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class PairFormType extends AbstractType {
    private $security;
    private $studentRepository;
    public function __construct(Security $security, StudentRepository $studentRepository) {
        $this->security = $security;
        $this->studentRepository = $studentRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder->add('second_student', EntityType::class, ['class' => Student::class, 'placeholder' => 'Pick a student', 'choices' => $this->studentRepository->findAllWithoutPair()]);
        if ($this->security->getUser()->getRoles()[0] == 'ROLE_ADMIN') {
            $builder->add('main_student', EntityType::class, ['class' => Student::class, 'placeholder' => 'Pick a student', 'choices' => $this->studentRepository->findAllWithoutPair()]);
        } else {
            $builder->add('main_student', EntityType::class, ['class' => Student::class, 'choices' => $this->studentRepository->findStudentsById($this->security->getUser()->getId())]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'pairForms'
        ]);
    }
}
