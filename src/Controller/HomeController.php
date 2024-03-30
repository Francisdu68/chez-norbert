<?php

namespace App\Controller;

use App\Repository\HouseRepository;
use App\Repository\WebSiteInformationRepository;
use App\Services\CaptchaVerifier;
use App\Services\CaptchaVerifierInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Contact;
use App\Form\ContactType;
use \ReCaptcha\ReCaptcha;

class HomeController extends AbstractController
{
    #[Route("/", name: "home.index")]
    public function index(HouseRepository $repository, WebSiteInformationRepository $informationRepository)
    {
        $rooms = $repository->findAllByType(1);
        $houses = $repository->findAllByType(0);

        $information = $informationRepository->findOne();

        return $this->render('home/index.html.twig', [
            'rooms' => $rooms,
            'houses' => $houses,
            'information' => $information
        ]);
    }



    /**
     * @Route({
     * "fr": "/a-propos",
     * "en": "/about"
     * }, name="home.about")
     */
    public function about()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            ]);
    }

    /**
     * @Route("/contact", name="home.contact")
     *
     * @param CaptchaVerifierInterface $verifier
     * @param Request $request
     * @param Swift_Mailer $mailer
     *
     * @param WebSiteInformationRepository $repository
     * @param WebSiteInformationRepository $informationRepository
     * @return Response
     */
    public function contact (CaptchaVerifierInterface $verifier, Request $request, Swift_Mailer $mailer, WebSiteInformationRepository $repository, WebSiteInformationRepository $informationRepository)
    {
        $information = $informationRepository->findOne();

        $contact = new Contact ();
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $verifier->verify($request->get('g-recaptcha-response'))) {
            $message = (new Swift_Message())
                ->setFrom($contact->getEmail())
                ->setTo($repository->findOne()->getEmail())
                ->setBody(
                    $this->renderView('mails/contact.html.twig', [
                        'contact' => $contact
                    ]),
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash('success', 'Le message a bien été envoyé');
            return $this->redirectToRoute('home.contact');
        }

        if($form->isSubmitted() &&  $form->isValid() && !$verifier->verify($request->get('g-recaptcha-response'))){
            $this->addFlash(
                'danger',
                'Captcha obligatoire'
            );
        }


        return $this->render ('home/contact.html.twig', [
            'form' => $form->createView(),
            'information' => $information
        ]);
    }
}
