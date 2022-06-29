<?php

namespace App\Controller;

use App\Entity\Food;
use App\Form\AddFoodType;
use App\Form\SearchFoodType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FoodController extends AbstractController
{
    /**
     * @Route("/food", name="app_food")
     */
    public function foodFunctions(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        //create new food and add to database
        $food = new Food();
        $form = $this->createForm(AddFoodType::class, $food);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $food = $form->getData();
            $entityManager->persist($food);
            $entityManager->flush();

            $notice = 'New Food named ' . $food->getName() . ' added';

            $this->addFlash('notice', $notice);
            return $this->redirectToRoute('app_food');
        }



        //search for food from the database

        $form2 = $this->createForm(SearchFoodType::class);
        $form2->handleRequest($request);

        $searchdata = $entityManager->getRepository(Food::class)->findAll();

        if ($form2->isSubmitted() && $form2->isValid()) {
            $data = $form2->getData();

            foreach ($data as $key => $value) {
                if ($value == null) {
                    unset($data[$key]);
                }
            }
            $searchdata = $entityManager->getRepository(Food::class)->findBy($data);


            return $this->render('food/index.html.twig', [
                'form' => $form->createView(),
                'form2' => $form2->createView(),
                'searchdata' => $searchdata
            ]);
        } else {
            return $this->render('food/index.html.twig', [
                'form' => $form->createView(),
                'form2' => $form2->createView(),
                'searchdata' => $searchdata
            ]);
        }
    }

    /**
     * @Route("/search", name="app_search")
     */
    public function search(Request $request, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();


        if ($request->get('value')) {
            $value = $request->get('value');

            $foods = $entityManager->getRepository(Food::class)->findLike($value); //userdefined function in the repository
            //echo("<p>".$value."</p>");


            /* $jsonData = array();  
            $idx = 0;  
            foreach($foods as $food) {  
               $temp = array(
                  'id' => $food->getId(),
                  'name' => $food->getName(),  
                  'price' => $food->getPrice(),  
               );   
               $jsonData[$idx++] = $temp;  
            }*/
            return new JsonResponse([
                'html' => $this->renderView('food/cardtemplate.html.twig', ['searchdata' => $foods])
            ]);
        } else {

            return $this->render('base.html.twig');
        }
    }


    /**
     * @Route("/update/{id}", name="update")
     */

    public function update(Request $request, ManagerRegistry $doctrine, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $entityManager = $doctrine->getManager();
        $fooddata = $entityManager->getRepository(Food::class)->find($id);
        $form = $this->createForm(AddFoodType::class, $fooddata);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $doctrine->getManager();
            $fooddata = $form->getData();

            // tell Doctrine you want to (eventually) save the userinfo (no queries yet)
            $entityManager->persist($fooddata);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();

            $this->addFlash('notice', 'Updated Successfully!!');

            return $this->redirectToRoute('app_food');
        }

        return $this->render('/food/update.html.twig', [

            'form' => $form->createView(),

        ]);
    }


    /**
     * @Route("/delete/{id}", name="delete")
     */

    public function delete(Request $request, ManagerRegistry $doctrine, $id)
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $entityManager = $doctrine->getManager();
        $fooddata = $entityManager->getRepository(Food::class)->find($id);

        $entityManager->remove($fooddata);
        $entityManager->flush();

        $this->addFlash('notice', 'Deleted Successfully!!');
        return $this->redirectToRoute('app_food');
    }
}
