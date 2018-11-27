<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchDateToDateTimeTransformer implements DataTransformerInterface
{
    public function transform($date)
    {
        if($date === null)
        {
            return '';
        }

        return $date->format('d/m/Y');
    }

    public function reverseTransform($frenchDate)
    {
        if($frenchDate === null)
        {
            // Exception
            throw new TransformationFailedException("Veuillez renseigner une date !");
        }
        
        // ex: $frenchDate = "20/09/2018"
        $date = \DateTime::createFromFormat('d/m/Y', $frenchDate);

        if($date === false)
        {
            // Exception
            throw new TransformationFailedException("Format de date incorrect !");
        }

        return $date;
    }
}