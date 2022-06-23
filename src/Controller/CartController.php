<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Food;
use App\Entity\OrderDetails;
use App\Entity\Orders;
use App\Entity\User;
use App\Form\AddToCartType;
use App\Form\CartCheckoutType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CartController extends AbstractController
{


    private $requestStack;
    private $security;
    private $doctrine;

    public function __construct(RequestStack $requestStack, Security $security, ManagerRegistry $doctrine)
    {
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->doctrine = $doctrine;
    }
    
    /**
     * @Route("/cart/add/{id}", name="app_cart")
     */
    public function addToCart(Request $request, $id): Response
    {   
        /**@var User $user */
        $user = $this->security->getUser();
        $foodid = $id;
        
        if(!empty($user))
        {
            $userId = $user->getId();
        }
        
        
        //dd($userId);

        $form = $this->createForm(AddToCartType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $cartdata = $form->getData();

            $quantity = $cartdata['quantity'];

            $session = $this->requestStack->getCurrentRequest()->getSession();

            if(!empty($session))
            {
                $cart = $session->get('carts');
                if(!empty($cart))
                {
                    
                    
                        $cart[$foodid] = $quantity;
                        $session->set('carts', $cart);
                    
                }
                else
                {
                    $cart = ["userid"=>$userId];
                    $cart[$foodid] = $quantity;
                    $session->set('carts', $cart);
                }
            }
            else
            {
                
                $cart = ["userid"=>$userId];
                $cart[$foodid] = $quantity;
                $session->set('carts', $cart);
            }
            
            //dd($sessiondata);
            return $this->redirectToRoute('app_food');
        }
        return $this->render('cart/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cart/remove/{id}", name="app_remove_from_cart")
     */
    public function removeFromCart($id)
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $cart = $session->get('carts');
        
        if(array_key_exists($id,$cart))
        {
            unset($cart[$id]);
            $session->set('carts',$cart);
        }

        return $this->redirectToRoute('app_my_cart');
    }

    /**
     * @Route("/cart", name="app_my_cart")
     */
    public function showcart(EntityManagerInterface $entityManager, Request $request)
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $cart = $session->get('carts');
        unset($cart['userid']);
        
        
        if(!empty($cart))
        {


            $form = $this->createForm(CartCheckoutType::class);
            $form->handleRequest($request);

            foreach ($cart as $id=>$quantity)
            {
                $food = $entityManager->getRepository(Food::class)->find($id);
            
                $cartitems[] = new Cart($food->getId(),$food->getName(),$food->getPrice(),$quantity);  
            
            }

            if ($form->isSubmitted() && $form->isValid())
            {
                //dd('hello submitted and valid');
                $formdata = $form->getData();
                $this->checkout($formdata, $cartitems);

                return $this->redirectToRoute('app_my_cart');
            }

            return $this->render('cart/mycart.html.twig',[
                'cart' => $cartitems,
                'form' => $form->createView(),
            ]);
        }
        else
        {
            return $this->render('cart/mycart.html.twig',[
                'cart' => null,
                'form' => null,
            ]);
        }

        //dd($cartitems);
        
    }

    public function checkout($formdata, $cartitems)
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $cart = $session->get('carts');

        $userid=$cart['userid'];
        $user = $this->doctrine->getRepository(User::class)->find($userid);

        $address = $formdata['address'];

        $order = new Orders();
        $order->setUsers($user);
        $order->setAddress($address);
        $tot=$this->calculateTotal($cartitems);
        $order->setTotalPrice($tot);
        $order->setAddress($address);

        unset($cart['userid']);
        $entityManager = $this->doctrine->getManager();

        foreach ($cart as $id=>$quantity)
        {
            $food = $this->doctrine->getRepository(Food::class)->find($id);
            $orderdetail = new OrderDetails();
            $orderdetail->setOrders($order);
            $orderdetail->setFood($food);
            $orderdetail->setQuantity($quantity);
            $entityManager->persist($orderdetail);

            
        }



        
        
        
        $entityManager->persist($order);
        
        $entityManager->flush();

        $session->remove('carts');
        $session->clear();

        return $this->redirectToRoute('app_my_cart');

    }

    public function calculateTotal($cartitems): string
    {
        /*if (array_key_exists('userid',$cart))
        {
            unset($cart['userid']);
        }*/
        $total = 0;
        foreach ($cartitems as $item)
        {
            $itemprice = floatval($item->getPrice()) * floatval($item->getQuantity());
            $total= $total + $itemprice;
        }

        return $total;
    }
}
