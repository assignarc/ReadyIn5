<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignUpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    { 
        $builder
            ->add('phone', NumberType::class, ['label' => 'Phone Number'])
            ->add('firstName',TextType::class,['label' => 'First Name'])
            ->add('lastName', TextType::class, ['label' => 'Last Name'])
            ->add('checkContact', SubmitType::class, ['label'=>'Check'])
            ->add('saveContact',SubmitType::class,['label'=>'Save and proceed'])
            ->add('adults',NumberType::class,['label'=>'Adults'])
            ->add('children',NumberType::class,['label'=>'Children'])
            ->add('requestTable',SubmitType::class,['label'=>'Request Table'])
            ;

    }

    // public function configureOptions(OptionsResolver $resolver): void
    // {
        
    //     $resolver->setDefaults([
    //         'data_class' => Customer::class,
    //     ]);
    // }
}
