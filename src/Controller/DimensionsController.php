<?php

namespace App\Controller;

use App\Entity\Dimensions;
use App\Form\DimensionsType;
use App\Repository\DimensionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dimensions')]
class DimensionsController extends AbstractController
{
    #[Route('/', name: 'app_dimensions_index', methods: ['GET'])]
    public function index(DimensionsRepository $dimensionsRepository): Response
    {
        return $this->render('dimensions/index.html.twig', [
            'dimensions' => $dimensionsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_dimensions_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dimension = new Dimensions();
        $form = $this->createForm(DimensionsType::class, $dimension);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($dimension);
            $entityManager->flush();

            return $this->redirectToRoute('app_dimensions_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dimensions/new.html.twig', [
            'dimension' => $dimension,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dimensions_show', methods: ['GET'])]
    public function show(Dimensions $dimension): Response
    {
        return $this->render('dimensions/show.html.twig', [
            'dimension' => $dimension,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dimensions_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dimensions $dimension, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DimensionsType::class, $dimension);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_dimensions_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dimensions/edit.html.twig', [
            'dimension' => $dimension,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dimensions_delete', methods: ['POST'])]
    public function delete(Request $request, Dimensions $dimension, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dimension->getId(), $request->request->get('_token'))) {
            $entityManager->remove($dimension);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dimensions_index', [], Response::HTTP_SEE_OTHER);
    }
}
