<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Staff;
use App\Repository\StaffRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ProjectFormType extends AbstractType {
    private $staffRepository;
    private $security;

    public function __construct(StaffRepository $staffRepository,Security $security) {
        $this->staffRepository = $staffRepository;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('technical_domains')
            ->add('is_taken')
            ->add('id_thematic')
            ->add('id_professional_domain')
            ->add('year');
        if (!$this->security->getUser()->getIsAdmin()) {
            $builder->add('id_teacher', EntityType::class, ['class' => Staff::class, 'placeholder' => 'Pick a teacher', 'choices' => $this->staffRepository->findById($this->security->getUser()->getId())]);
        } else {
            $builder->add('id_teacher');
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'translation_domain' => 'projectForms'
        ]);
    }
}
