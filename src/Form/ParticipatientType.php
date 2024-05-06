<?php

namespace App\Form;

use App\Entity\Participatient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;

class ParticipatientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_par', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est requis.']),
                    new Regex([
                        'pattern' => '/^[a-zA-Z]+$/',
                        'message' => 'Le nom doit contenir uniquement des lettres.',
                    ]),
                ],
            ])
            ->add('prenom_par', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est requis.']),
                    new Regex([
                        'pattern' => '/^[a-zA-Z]+$/',
                        'message' => 'Le prénom doit contenir uniquement des lettres.',
                    ]),
                ],
            ])
            ->add('age_par', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['message' => "L'âge est requis."]),
                    new Range([
                        'min' => 5,
                        'max' => 16,
                        'minMessage' => 'L\'âge doit être au moins de 5 ans.',
                        'maxMessage' => 'L\'âge ne doit pas dépasser 16 ans.',
                    ]),
                ],
            ])
            ->add('event')
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse e-mail est requise.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participatient::class,
        ]);
    }
}
