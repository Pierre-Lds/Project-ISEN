<?php

namespace App\Form;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PairFormType extends AbstractType
{
    private $studentRepository;
    public function __construct(StudentRepository $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('main_student', EntityType::class, ['class' => Student::class, 'placeholder' => 'Pick a student', 'choices' => $this->studentRepository->findAllWithoutPair()])
            ->add('second_student', EntityType::class, ['class' => Student::class, 'placeholder' => 'Pick a student', 'choices' => $this->studentRepository->findAllWithoutPair()])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'pairForms'
        ]);
    }
}
