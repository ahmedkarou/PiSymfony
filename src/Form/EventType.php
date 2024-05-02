<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\GreaterThan;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter the event name.']),
                ],
            ])
            ->add('capacite', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter the capacity.']),
                ],
            ])
            ->add('localization', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter the localization.']),
                ],
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter the description.']),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Event Type',
                'choices' => [
                    'FootBall' => 'FootBall',
                    'BasketBall' => 'BasketBall',
                    'HandBall' => 'Handball',
                    'Tennis' => 'Tennis',
                    'Hockey' => 'Hockey',
                    'Gymnastic' => 'Gymnastic',
                    'VolleyBall' => 'VolleyBall',
                    'Running' => 'Running',
                ],
                'placeholder' => 'Choose an event type',  // Optional
                'required' => true, // Mark the field as required
                'constraints' => [
                    new NotBlank(['message' => 'Please choose an event type.']),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Your Image (Images only)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Please enter an image.']),
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image',
                    ])
                ],
            ])
            ->add('date', DateType::class, [
                'data' => new \DateTime(),
                'html5' => true,
                'widget' => 'single_text',
                
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
