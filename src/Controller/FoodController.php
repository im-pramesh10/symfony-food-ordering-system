<?php

namespace App\Controller;

use App\Entity\Food;
use App\Form\AddFoodType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FoodController extends AbstractController
{
    /**
     * @Route("/food", name="app_food")
     */
    public function addFood(Request $request, ManagerRegistry $doctrine): Response
    {

        $food = new Food();
        $form = $this->createForm(AddFoodType::class, $food);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $food = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($food);
            $entityManager->flush();

            $notice='New Food named '.$food->getName().' added';
            $this->addFlash('notice', $notice);
            dd($food);
            return $this->redirectToRoute('app_food');
        }


        return $this->render('food/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/food/search", name="app_food_search")
     */
    public function searchFood(Request $request, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();

            foreach ($data as $key => $value) {
                if($value == null){
                    unset($data[$key]);
                }
            }

            $entityManager = $doctrine->getManager();
            
            $foods = $entityManager->getRepository(Food::class)->findBy($data);
            
            dd($foods);

            return $form;
        }

        return $this->render('food/index.html.twig');
    }

    public function renderPage()
    {

        return $this->render('food/index.html.twig');
    }


}
