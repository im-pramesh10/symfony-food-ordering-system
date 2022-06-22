<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddToCartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity',NumberType::class, ['attr'=> array('placeholder'=>'Quantity', 'class'=>'form-control mb-1', 'min'=>1), 'required'=>true])
            ->add('AddToCart',SubmitType::class, ['attr'=>array('class'=>'btn btn-primary text-white mb-1'), 'label'=>'Add to Cart'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
