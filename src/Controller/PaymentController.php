<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Product;
use Stripe\Checkout\Session;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    #[Route('/checkout', name: 'app_payment_checkout')]
    public function checkout($stripe_SK, SessionInterface $session_panier, ManagerRegistry $doctrine): Response
    {
        Stripe::setApiKey($stripe_SK);

        $panier = $session_panier->get('panier', []);

        $panier_data = [];
        foreach ($panier as $id => $quantity) {
            $panier_data[] = [
                "product" => $doctrine->getRepository(Product::class)->find($id),
                "quantity" => $quantity
            ];
        }


        foreach ($panier_data as $id => $value) {
            $line_items[] = [
                "price_data" => [
                    "currency" => "eur",
                    "product_data" => [
                        'name' => $value['product']->getname(),
                    ],
                    "unit_amount" => $value['product']->getPrice() * 100,
                ],
                "quantity" => $value['quantity']
            ];
        }

        $session = Session::create([
            'line_items' => [$line_items],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_success_url', [
                'panier_data' => $panier_data
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }

    #[Route('/checkout/success', name: 'app_success_url')]
    public function success(SessionInterface $session)
    {
        $session->remove('panier');

        return $this->render('payment/success.html.twig');
    }

    #[Route('/checkout/cancel', name: 'app_cancel_url')]
    public function cancel()
    {
        return $this->render('payment/cancel.html.twig');
    }
}
