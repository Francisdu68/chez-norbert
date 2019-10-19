<?php

namespace App\Controller\Admin;

use App\Entity\House;
use App\Form\HouseType;
use App\Repository\HouseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_USER")
 */
class AdminHouseController extends AbstractController
{
    /**
     * @Route("/admin/house", name="admin.house.index")
     * @param HouseRepository $repository
     * @return Response
     */
    public function index (HouseRepository $repository) {
        $houses = $repository->findAll();

        return $this->render('admin/house/index.html.twig', [
            'houses' => $houses
        ]);
    }

    /**
     * @Route("/admin/house/create", name="admin.house.create")
     * @Route("/admin/house/{id}", name="admin.house.update", methods="GET|POST")
     *
     * @param Request $request
     * @param HouseRepository $repository
     * @param EntityManagerInterface $manager
     * @param House|null $house
     * @return Response
     */
    public function create(Request $request, HouseRepository $repository, EntityManagerInterface $manager, House $house = null)
    {
        if (!$house) {
            $house = new House();
        }

        $form = $this->createForm(HouseType::class, $house, [
            'edit' => !!$house->getId(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($house);
            $manager->flush();
            return $this->redirectToRoute('admin.house.index');
        }

        return $this->render('admin/house/form.html.twig', [
            'form' => $form->createView(),
            'house' => $house
        ]);
    }

    /**
     * @Route("/admin/house/{id}", name="admin.house.delete", methods="DELETE")
     *
     * @param House $house
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete (House $house, EntityManagerInterface $manager, Request $request) {
        $isCsrfValid = $this->isCsrfTokenValid(
            'delete' . $house->getId(),
            $request->get('_token')
        );

        if (!$isCsrfValid) {
            $this->addFlash('danger', "Le jeton CSRF n'est pas valide.");
            return $this->redirectToRoute('admin.house.index');
        }

        $manager->remove($house);
        $manager->flush();

        $this->addFlash('success', 'Le ' . $house->getType() . ' a été supprimé.');
        return $this->redirectToRoute('admin.house.index');
    }
}
