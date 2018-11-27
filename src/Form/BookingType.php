<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\DataTransformer\FrenchDateToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingType extends ApplicationType
{
    private $transformer;
    
    public function __construct(FrenchDateToDateTimeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', TextType::class, 
            [
                'label' => "Date d'arrivée",
                'attr' => ['placeholder' => "jj/mm/aaaa"]
            ])
            ->add('endDate', TextType::class, 
            [
                'label' => "Date de départ",
                'attr' => ['placeholder' => "jj/mm/aaaa"]
            ])
            ->add('comments' , TextareaType::class, 
            [
                'label' => false,
                'attr' => ['placeholder' => "Vous pouvez laisser un message à l'hôte"],
                'required' => false
            ])
        ;

        $builder->get('startDate')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
