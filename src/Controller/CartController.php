<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Food;
use App\Entity\User;
use App\Form\AddToCartType;
use Doctrine\ORM\EntityManagerInterface;
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
    
    public function __construct(RequestStack $requestStack, Security $security)
    {
        $this->requestStack = $requestStack;
        $this->security = $security;
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
    public function showcart(EntityManagerInterface $entityManager)
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $cart = $session->get('carts');
        unset($cart['userid']);

        foreach ($cart as $id=>$quantity)
        {
            $food = $entityManager->getRepository(Food::class)->find($id);
            
            $cartitems[] = new Cart($food->getId(),$food->getName(),$food->getPrice(),$quantity);  
            
        }

        //dd($cartitems);
        return $this->render('cart/mycart.html.twig',[
            'cart' => $cartitems,
        ]);
    }
}
