<?php

namespace App\Form;

use App\Entity\Gift;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GiftFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du cadeau<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('url', UrlType::class, [
                'label' => 'Lien du produit à titre d\'exemple<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('details', TextType::class, [
                'label' => 'Options de personnalisation<span class="text-danger"> *</span>',
                'label_html' => true,
            ])
            ->add('price', IntegerType::class,  [
                'label' => 'Prix<span class="text-danger"> *</span>',
                'label_html' => true,
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
