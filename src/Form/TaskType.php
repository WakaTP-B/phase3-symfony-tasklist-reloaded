<?php

namespace App\Form;

use App\Entity\Folder;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\TypeInfo\Type\EnumType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('status', EnumType::class, [
                'class' => TaskStatus::class,
            ])
            ->add('isPinned')
            ->add('User', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('Folder', EntityType::class, [
                'class' => Folder::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
