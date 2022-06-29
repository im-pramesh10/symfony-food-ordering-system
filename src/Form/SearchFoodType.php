<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFoodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', NumberType::class, ['attr' => array('placeholder' => 'id', 'class' => 'form-control mb-1'), 'required' => false])
            ->add('name', TextType::class, ['attr' => array('placeholder' => 'Search By Food Name', 'class' => 'form-control mb-1'), 'required' => false])
            ->add('price', NumberType::class, ['attr' => array('placeholder' => 'Price', 'class' => 'form-control mb-1'), 'required' => false])
            ->add('search', SubmitType::class, ['attr' => array('class' => 'btn btn-primary text-white mb-1')]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //configure resolver here
        ]);
    }
}
