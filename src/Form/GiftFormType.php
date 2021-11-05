<?php

namespace App\Form;

use App\Entity\Gift;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class GiftFormType extends AbstractType
{
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
                'label' => 'Nom du cadeau<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('url', UrlType::class, [
                'constraints' => array(
                    new Regex(
                        "%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu",
                        "URL invalide"
                    ),
                    new NotBlank([
                        'message' => "Ce champ ne peut pas être vide."
                    ])
                ),
                'label' => 'Lien du produit à titre d\'exemple<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('details', TextType::class, [
                'label' => 'Options de personnalisation',
                'label_html' => true,
                'required' => false
            ])
            ->add('price', NumberType::class,  [
                'constraints' => array(
                    new NotBlank([
                        'message' => "Ce champ ne peut pas être vide."
                    ])
                ),
                'label' => 'Prix<span class="text-danger"> *</span>',
                'label_html' => true,
                'invalid_message' => 'Le tarif est invalide, merci de saisir une valeur numérique',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Gift::class,
        ]);
    }
}
