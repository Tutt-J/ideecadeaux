<?php

namespace App\Form;

use App\Entity\Child;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChildFormType extends AbstractType
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
            ->add('save', SubmitType::class, [
                'label' => 'Ajouter',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Child::class,
        ]);
    }
}
