<?php

namespace App\Form;

use App\Entity\Folder;
use App\Entity\Task;
use App\Entity\User;
use App\Enum\TaskStatus;
use App\Entity\Priority;
use App\Repository\PriorityRepository;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            ->add('title', null, [
                'label' => 'Titre de la tâche',
                'attr' => ['placeholder' => 'Ex: Faire les courses'],
            ])
            ->add('priority', EntityType::class, [
                'class' => Priority::class,
                'choice_label' => 'name',
                'label' => 'Priorité',
                'placeholder' => 'Sélectionner une priorité',
                'query_builder' => fn(PriorityRepository $repo) => $repo
                    ->createQueryBuilder('p')
                    ->where('p.User IS NULL OR p.User = :user')
                    ->setParameter('user', $user)
                    ->orderBy('p.User', 'ASC')
                    ->addOrderBy('p.id', 'ASC'),
            ])
            ->add('Folder', EntityType::class, [
                'class' => Folder::class,
                'choice_label' => 'name',
                'label' => 'Dossier (optionnel)',
                'placeholder' => 'Sélectionner un dossier',
                'required' => false,
                'query_builder' => fn($repo) => $repo->createQueryBuilder('f')
                    ->where('f.User = :user')
                    ->setParameter('user', $user),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
        $resolver->setRequired('user');
        $resolver->setAllowedTypes('user', User::class);
    }
}
