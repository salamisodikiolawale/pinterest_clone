<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PinsController extends AbstractController
{
    /**
     * @Route("/", name="app_pins_home")
     */

    public function index(PinRepository $repo): Response
    {
        return $this->render("pins/index.html.twig", ['pins' => $repo->findAll()]);
    }

    /**
    * @Route("/pins/{id<[0-9]+>}", name="app_pins_show", methods={"GET", "POST"})
    */
    public function show(PinRepository $repo, int $id): Response
    {
        $pin = $repo->find($id);
        if (!$pin) 
        {
            throw $this->createNotFoundException('Pin #'. $id .' not found');
            
        }
        return $this->render("pins/show.html.twig", compact('pin'));
    }

    /**
     * @Route("/pins/create", name="app_pins_create", methods={"GET","POST"})
     */

    public function createPin(Request $request, EntityManagerInterface $em): Response
    {
        $pin = new Pin;

        $form = $this->createFormBuilder($pin)
            ->add('title', null, ['attr' => ['autofocus' => true]])
            ->add('description', null, ['attr'=>['rows'=>'10', 'cols'=>'50']])
            //->add('Submit', SubmitType::class, ['label' => 'Create Pin'])
            ->getForm()//is important for recupere the form if finish to create
        ;

        //Recupering of request
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            //Recupere all data of form
            /*$data = $form->getData();
            $pin = new Pin;
            $pin->setTitle($data['title']);
            $pin->setDescription($data['description']);*/
            $em->persist($pin);
            $em->flush();

            return $this->redirectToRoute('app_pins_home');
        }
        
        return $this->render("pins/create.html.twig", [
            'monFormulaire' => $form->createView()
        ]);
    }


}
