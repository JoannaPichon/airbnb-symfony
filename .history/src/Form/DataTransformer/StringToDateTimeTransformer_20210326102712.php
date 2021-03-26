<?php

namespace App\Form\DataTransformer;

use \DateTime;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class StringToDateTimeTransformer implements DataTransformerInterface
{


	//lorsque lon passe des données au formulaire
	public function transform($date)
	{
		if ($date != null) {
			return $date->format('d/m/Y');
		}
		
	}

	public function reverseTransform($strDate)
	{
		dump($strDate);
		$date = \DateTime::createFromFormat('d/m/Y',$strDate);
		/:
		$date->setTime(0,0,0);
		return $date;
	}
}