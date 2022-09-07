<?php

namespace App\Form;

use App\Entity\Emprunteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EmprunteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', UserType::class)
            ->add('nom', TextType::class, [
                'label' => 'nom',
            ])
            ->add('prenom', TextType::class, [
                'label' => 'prénom',
            ])
            ->add('tel', TextType::class, [
                'label' => 'téléphone',
            ])
            ->add('actif', ChoiceType::class, [
                'label' => 'status emprunteur',
                'choices' => [
                    'Actif' => 1,
                    'Inactif' => 0,
                ],
                'multiple' => false,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Emprunteur::class,
        ]);
    }
}
