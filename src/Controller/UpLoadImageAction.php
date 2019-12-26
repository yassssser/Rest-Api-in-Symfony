<?php 

namespace App\Controller;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Image;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class UpLoadImageAction {

    private $formFactory;
    private $em;
    private $validator;

    public function __construct(FormFactoryInterface  $formFactory, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->validator = $validator;
    }

    public function __invoke(Request $request)
    {
        // create a new image instance
        $img = new Image();

        // Validate the form
        $form = $this->formFactory->create(ImageType::class, $img);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($img);
            $this->em->flush();

            $img->setFile(null);
            return $img;
        }

        throw new ValidationException(
            $this->validator->validate($img)
        );
    }
}