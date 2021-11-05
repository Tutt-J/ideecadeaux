<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
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
use Symfony\Component\Validator\Constraints\Country;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Rollerworks\Component\PasswordStrength\Validator\Constraints as RollerworksPassword;

class RegistrationFormType extends AbstractType
{
    const NOTEMPTY_MESSAGE ="Ce champ ne peut pas être vide.";

    public function setFieldForName($name){
        return [
            'constraints' => [
                new Length([
                    'normalizer' => 'trim',
                    'min' => 2,
                    'max' => 25,
                    'minMessage' => 'Le '.$name.' doit au moins contenir {{ limit }} caractères',
                    'maxMessage' => 'Le '.$name.' ne doit pas dépasser {{ limit }} caractères',
                    'allowEmptyString' => false,
                ]),
                new NotBlank([
                    'message' => SELF::NOTEMPTY_MESSAGE
                ])
            ],
            'label' => $name.'<span class="text-danger"> *</span>',
            'label_html' => true
        ];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, $this->setFieldForName("Prénom"))
            ->add('lastName', TextType::class, $this->setFieldForName("Nom"))
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email([
                        'message'=>'L\'adresse e-mail "{{ value }}" est invalide.'
                    ]),
                    new NotBlank([
                        'message' => SELF::NOTEMPTY_MESSAGE
                    ])
                ],
                'label' => 'Email<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('street', TextType::class, [
                'constraints' => [
                    new Length([
                        'normalizer' => 'trim',
                        'min' => 4,
                        'max' => 255,
                        'minMessage' => 'La rue doit au moins contenir {{ limit }} caractères.',
                        'maxMessage' => 'La rue ne doit pas dépasser {{limit}} caractères.',
                        'allowEmptyString' => false,
                    ]),
                    new NotBlank([
                        'message' => SELF::NOTEMPTY_MESSAGE
                    ])
                ],
                'label' => 'Numéro et nom de la rue<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('postalCode', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => SELF::NOTEMPTY_MESSAGE
                    ])
                ],
                'label' => 'Code postal<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new Length([
                        'normalizer' => 'trim',
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'La ville doit au moins contenir {{ limit }} caractères.',
                        'maxMessage' => 'La ville ne doit pas dépasser {{limit}} caractères.',
                        'allowEmptyString' => false,
                    ]),
                    new NotBlank([
                        'message' => SELF::NOTEMPTY_MESSAGE
                    ])
                ],
                'label' => 'Ville<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('country', CountryType::class, [
                'constraints' => [
                    new Length([
                        'normalizer' => 'trim',
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le pays doit au moins contenir {{ limit }} caractères.',
                        'maxMessage' => 'La pays ne doit pas dépasser {{limit}} caractères.',
                        'allowEmptyString' => false,
                    ]),
                    new NotBlank([
                        'message' => SELF::NOTEMPTY_MESSAGE
                    ]),
                    new Country([
                        'message'=>'Le pays est invalide.'
                    ])
                ],
                'preferred_choices' => array('FR'),
                'label' => 'Pays<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'constraints' => [
                    new Length([
                        'normalizer' => 'trim',
                        'max' => 255,
                        'maxMessage' => 'Le mot de passe ne doit pas dépasser {{limit}} caractères',
                        'allowEmptyString' => false,
                    ]),
                    new NotBlank([
                        'message' => SELF::NOTEMPTY_MESSAGE
                    ]),
                    new NotCompromisedPassword([
                        'message' => 'Ce mot de passe a été divulgué lors d\'\'une violation de données, il ne doit pas être utilisé. Veuillez utiliser un autre mot de passe.'
                    ]),
                    new RollerworksPassword\PasswordRequirements([
                        'minLength' => 8,
                        'tooShortMessage' => 'Le mot de passe doit contenir au moins {{length}} caractères.',
                        'requireLetters' => true,
                        'missingLettersMessage' => 'Le mot de passe doit comprendre au moins une lettre.',
                        'requireCaseDiff' => true,
                        'requireCaseDiffMessage' => 'Le mot de passe doit inclure des lettres majuscules et minuscules.',
                        'requireNumbers' => true,
                        'missingNumbersMessage' => 'Le mot de passe doit inclure au moins un chiffre.',
                        'requireSpecialCharacter' => true,
                        'missingSpecialCharacterMessage' => 'Le mot de passe doit contenir au moins un caractère spécial.',
                    ])
                ],
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'Mot de passe<span class="text-danger"> *</span>',
                    'label_html' => true,
                    'help' => 'Le mot de passe doit être de 8 caractères minimum et contenir au moins 1 chiffre, 1 lettre, 1 majuscule et 1 caractère spécial',
                    ],
                'second_options' => [
                    'label' => 'Répétez le mot de passe<span class="text-danger"> *</span>',
                    'label_html' => true,
                    'help' => 'Le mot de passe doit être de 8 caractères minimum et contenir au moins 1 chiffre, 1 lettre, 1 majuscule et 1 caractère spécial',
                    ],
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
            'constraints'     => array(
                new UniqueEntity(array(
                    'fields' => array('email'),
                    'message' => 'Cette adresse e-mail est déjà associée à un compte'
                ))
            )
        ]);
    }
}
