<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration("Prénom", "Un prénom"))
            ->add('lastName', TextType::class, $this->getConfiguration("Nom", "Un nom"))
            ->add('pseudo',TextType::class, $this->getConfiguration("Pseudo", "Un pseudo"))
            ->add('email', EmailType::class, $this->getConfiguration("Email", "Une adresse mail"))
            ->add('password', RepeatedType::class, 
            [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe ne correspondent pas.',
                'required' => true,
                'first_options' => $this->getConfiguration("Mot de passe", "Un mot de passe"),
                'second_options' => 
                    $this->getConfiguration("Confirmation de mot de passe", "Confirmer le mot de passe")
            ])
            ->add('introduction', TextType::class, 
            $this->getConfiguration("Introduction", "Présentez vous en quelques mots"))
            ->add('description', TextareaType::class, 
            $this->getConfiguration("Description", "Présenter vous plus en détails"))
            ->add('picture', UrlType::class, $this->getConfiguration("Photo de profil", " Une photo"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
