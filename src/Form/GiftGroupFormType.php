<?php

namespace App\Form;

use App\Entity\Child;
use App\Entity\GiftGroup;
use App\Repository\ChildRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class GiftGroupFormType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Length([
                        'normalizer' => 'trim',
                        'min' => 2,
                        'max' => 25,
                        'minMessage' => 'Le nom doit au moins contenir {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne doit pas dépasser {{ limit }} caractères',
                        'allowEmptyString' => false,
                    ]),
                    new NotBlank([
                        'message' => "Ce champ ne peut pas être vide."
                    ])
                ],
                'label' => 'Nom de la liste<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('expireDate', DateType::class, [
                'label' => 'Date d\'expiration<span class="text-danger"> *</span>',
                'label_html' => true,
                'widget' => 'choice',
                'format' => 'ddMMyyyy',
                'html5' => false,
                'years' => range(date('Y'), date('Y')+2),
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                ],
                ])
            ->add('child', EntityType::class, [
                'class' => Child::class,
                'query_builder' => function (ChildRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.parent = :id')
                        ->setParameter('id', $this->security->getUser()->getId())
                        ;
                },
                'placeholder' => 'Choisir un enfant',
                'label' => 'Choix de l\'enfant, le cas échéant',
                'label_html' => true,
                'choice_label' => 'firstName',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Créer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GiftGroup::class,
        ]);
    }
}
