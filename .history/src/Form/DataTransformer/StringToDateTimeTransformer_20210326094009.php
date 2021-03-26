<?php

namespace App\Form\DataTransformer;

use DateTime;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class StringToDateTimeTransformer implements DataTransformerInterface
{


	//lorsque lon passe des données au formulaire
	public function transform($date)
	{
		return $date->format('d/m/Y');
	}

	public function reverseTransform($strDate)
	{
		$date = new DateTime($strDate);
		
		return $date;
	}
}