<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, 
            $this->getConfiguration("Mot de passe actuel", "Saisissez votre mot de passe actuel"))
            ->add('newPassword', RepeatedType::class,
            [
                'type' => PasswordType::class,
                'invalid_message' => "Les deux mots de passe ne correspondent pas",
                'required' => true,
                'first_options' => 
                    $this->getConfiguration("Nouveau mot de passe", "Votre nouveau mot de passe"),
                'second_options' => 
                    $this->getConfiguration("Confirmation du mot de passe", "Confirmer le mot de passe")
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
