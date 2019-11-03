<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\House;
use App\Form\BookingType;
use App\Repository\HouseRepository;
use App\Repository\WebSiteInformationRepository;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    /**
     * @Route("/booking/{id}", name="booking.index")
     * @param Request $request
     * @param House $house
     * @param Swift_Mailer $mailer
     * @param WebSiteInformationRepository $repository
     * @return Response
     */
    public function index(Request $request, House $house, Swift_Mailer $mailer, WebSiteInformationRepository $repository)
    {
        $booking = new Booking();
        $bookingForm = $this->createForm(BookingType::class, $booking, ['action' => $this->generateUrl('booking.index', ['id' => $house->getId()])]);

        $bookingForm->handleRequest($request);

        if ($bookingForm->isSubmitted()) {

            if ($bookingForm->isValid()) {

                $message = (new Swift_Message())
                    ->setFrom($booking->getEmail())
                    ->setTo($repository->findOne()->getEmail())
                    ->setBody(
                        $this->renderView('mails/booking.html.twig', [
                            'booking' => $booking,
                            'house' => $house
                        ]),
                        'text/html'
                    );

                $mailer->send($message);

                $this->addFlash('success', 'La reservation a bien été envoyée');
            }
            return $this->redirectToRoute('houses.get', [ 'slug' => $house->getSlug() ]);
        }

        return $this->render ('booking/index.html.twig', [
           'form' => $bookingForm->createView()
        ]);
    }
}
