<?php

namespace App\Controller;

use App\Entity\Orders;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class OrderController extends AbstractController
{


    private $security;
    private $doctrine;

    public function __construct(Security $security, ManagerRegistry $doctrine)
    {
        $this->security = $security;
        $this->doctrine = $doctrine;
    }
    /**
     * @Route("/order", name="app_order")
     */
    public function showUserOrders(): Response
    {
        /**@var User $user */
        $user = $this->security->getUser(); //getting the logged in user

        $order = $this->doctrine->getRepository(Orders::class)->findBy(['users'=>$user]);
       //dd($order);
       //dd($order[0]->getOrderDetails()->getValues());
        return $this->render('order/index.html.twig', [
            'orderlist' => $order,
        ]);
    }

    /**
     * @Route("/order/admin", name="app_order_admin")
     */
    public function showAllOrders(): Response
    {

        $order = $this->doctrine->getRepository(Orders::class)->findAll();
       //dd($order);
       //dd($order[0]->getOrderDetails()->getValues());
        return $this->render('order/index.html.twig', [
            'orderlist' => $order,
        ]);
    }
}
