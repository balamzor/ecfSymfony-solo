<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'app_payment')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }
    #[Route('/payment/checkout', name: 'app_checkout', methods: ['POST'])]
    public function checkout(Request $request): Response
    {

        $data = $request->request->all();
        $prenom = $request->request->get('firstName');
        $nom = $request->request->get('lastName');
        $cbNumbers = $request->request->get('cbNumbers');
        $cbDate = $request->request->get('cbDate');
        $cbVerif = $request->request->get('cbVerif');
        $valid = $this->isValid(str_split($cbNumbers));
        $csrf = $request->request->get('csrf');
        $token = $request->get('token');



        if ($csrf === $token) {


        }


        return $this->render('payment/checkout.html.twig', [
            'valid' => $valid,
        ]);
    }
    private function isValid(array $cardNumber): bool
    {
        $sum = 0;
        $parity = count($cardNumber) % 2;

        foreach ($cardNumber as $i => $num) {
            if ($i % 2 !== $parity) {
                $sum += $num;
            } elseif ($num > 4) {
                $sum += 2 * $num - 9;
            } else {
                $sum += 2 * $num;
            }
        }

        if ($cardNumber[count($cardNumber) - 1] === ((10 - ($sum % 10)) % 10)) {
            return true;
        } else {
            return false;
        }

        // return $cardNumber[count($cardNumber) - 1] === ((10 - ($sum % 10)) % 10);
    }

}
