<?php

namespace App\Services;

use App\Entity\ImageUpload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImagesUploadService extends AbstractController
{
	public function upload($ad, $manager) {
		foreach ($ad->file as $file) {
			dump($ad);
			// $position_point = strpos($file->getClientOriginalName(), '.');
			// $original_name = substr($file->getClientOriginalName(), 0, $position_point);
			$original_name = preg_replace('#\.(jpg|png|jpeg)$#', '', $file->getClientOriginalName() );
			dump($original_name);
			dump($file->guessExtension());
			$filename = uniqid().'.'.$file->guessExtension();
			dump($filename);
			
			$upload = new ImageUpload();

			$upload -> setName($original_name)
					-> setUrl('/uploads/'.$filename)
					-> setAd($ad);

			$manager -> persist($upload);
			
			
			//deplacement du fichier dans le dossier uploads
			$file->move(
				$this->getParameter('files_directory'),
				$filename
			);  
		}
	}
	
}