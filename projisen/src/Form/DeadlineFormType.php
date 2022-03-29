<?php

namespace App\Form;

use App\Entity\Deadline;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeadlineFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pair')
            ->add('new_projects')
            ->add('choose_projects_1')
            ->add('choose_projects_2')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Deadline::class,
        ]);
    }
}
