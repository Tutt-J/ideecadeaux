<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('firstName', TextType::class, [
                'label' => 'Prénom<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('street', TextType::class, [
                'label' => 'Numéro et nom de la rue<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'invalid_message' => 'Les deux mots de passe doivent être identiques',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe<span class="text-danger"> *</span>', 'label_html' => true,],
                'second_options' => ['label' => 'Répétez le mot de passe<span class="text-danger"> *</span>', 'label_html' => true,],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte les <a href="">conditions générales de vente</a><span class="text-danger"> *</span>',
                'label_html' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'S\'inscrire',
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
