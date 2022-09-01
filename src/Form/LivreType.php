<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Livre;
use App\Entity\Auteur;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class LivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class)
            ->add('annee_edition', NumberType::class)
            ->add('nombre_pages', NumberType::class)
            ->add('code_isbn', TextType::class)
            ->add('auteur', EntityType::class, [
                'class' => Auteur::class,

                'choice_label' => function (Auteur $object) {
                    return "{$object->getNom()} {$object->getPrenom()} ({$object->getId()})";
                },

                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->orderBy('a.nom', 'ASC');
                },
            ])
            ->add('genre', EntityType::class, [
                'class' => Genre::class,
            
                'choice_label' => function (Genre $object) {
                    return "{$object->getNom()} ({$object->getId()})";
                },
            
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,

                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('g')
                        ->orderBy('g.nom', 'ASC');
                },

                'attr' => [
                    'class' => 'checkboxes-with-scroll',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}
