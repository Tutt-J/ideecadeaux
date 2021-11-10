<?php

namespace App\Form;

use App\Entity\Pot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PotFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', NumberType::class,  [
                'constraints' => array(
                    new NotBlank([
                        'message' => "Ce champ ne peut pas être vide."
                    ])
                ),
                'label' => 'Montant<span class="text-danger"> *</span>',
                'label_html' => true,
                'invalid_message' => 'Le montant est invalide, merci de saisir une valeur numérique',
            ])
            ->add('id', HiddenType::class, [
                'mapped' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Participer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pot::class,
        ]);
    }
}
