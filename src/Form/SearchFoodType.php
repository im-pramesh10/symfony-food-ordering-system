<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFoodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id',NumberType::class, ['attr'=> array('placeholder'=>'id', 'class'=>'form-control')])
            ->add('name',TextType::class, ['attr'=> array('placeholder'=>'Name', 'class'=>'form-control')])
            ->add('price', NumberType::class, ['attr'=> array('placeholder'=>'Price', 'class'=>'form-control')])
            ->add('submit',SubmitType::class,['attr'=>array('class'=>'btn btn-info mt-2 text-white')])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
