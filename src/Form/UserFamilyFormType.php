<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserFamilyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('uuid', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => "Ce champ ne peut pas être vide."
                    ]),
                ],
                'label' => 'Identifiant de la famille<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Rejoindre',
            ])
        ;
    }


}
