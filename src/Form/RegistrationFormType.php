<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => array(
                    'placeholder' => 'enter username..'
                ),
                'label' => false
            ])
            ->add('email', TextType::class, [
                'attr' => array(
                    'placeholder' => 'enter email..'
                ),
                'label' => false
            ])
            ->add('birthday', BirthdayType::class,[
                'widget' => 'choice',
                'input'  => 'datetime_immutable',
                'label' => false
            ])
            ->add('profileImg', FileType::class, array(
                'required' => false,
                'mapped' => false,
                'label' => false
            ))
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
                'label' => false
            ])
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,

                    'first_options' => [
                        'attr' => [
                            'placeholder' => 'enter password',
                        ],
                        'label' => false,
                    ],

                    'second_options' => [
                        'attr' => [
                            'placeholder' => 'confirm password',
                        ],
                        'label' => false,
                    ],


                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'new-password',
                                'placeholder' => 'enter your password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                    'label' => false
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
