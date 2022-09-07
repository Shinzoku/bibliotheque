<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Emprunteur;
use App\Form\EmprunteurType;
use App\Repository\EmprunteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Require ROLE_ADMIN for all the actions of this controller
 */
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/emprunteur')]
class EmprunteurController extends AbstractController
{
    #[Route('/', name: 'app_emprunteur_index', methods: ['GET'])]
    public function index(EmprunteurRepository $emprunteurRepository): Response
    {
        return $this->render('emprunteur/index.html.twig', [
            'emprunteurs' => $emprunteurRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_emprunteur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager, EmprunteurRepository $emprunteurRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $emprunteur = new Emprunteur();
        $form = $this->createForm(EmprunteurType::class, $emprunteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $user = $emprunteur->getUser();
            $notHashedPassword = $user->getPassword();
            $hashedPassword = $passwordHasher->hashPassword($user, $notHashedPassword);
            $user->setPassword($hashedPassword);

            $manager->persist($user);
            
            $emprunteurRepository->add($emprunteur, true);

            $this->addFlash('success', 'Nouvel utilisateur créé !');

            return $this->redirectToRoute('app_emprunteur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('emprunteur/new.html.twig', [
            'emprunteur' => $emprunteur,
            'form' => $form,
            // 'formUser' => $formUser,
        ]);
    }

    #[Route('/{id}', name: 'app_emprunteur_show', methods: ['GET'])]
    public function show(Emprunteur $emprunteur): Response
    {
        return $this->render('emprunteur/show.html.twig', [
            'emprunteur' => $emprunteur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_emprunteur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Emprunteur $emprunteur, EmprunteurRepository $emprunteurRepository): Response
    {
        $form = $this->createForm(EmprunteurType::class, $emprunteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emprunteurRepository->add($emprunteur, true);

            return $this->redirectToRoute('app_emprunteur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('emprunteur/edit.html.twig', [
            'emprunteur' => $emprunteur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_emprunteur_delete', methods: ['POST'])]
    public function delete(Request $request, Emprunteur $emprunteur, EmprunteurRepository $emprunteurRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$emprunteur->getId(), $request->request->get('_token'))) {
            $emprunteurRepository->remove($emprunteur, true);
        }

        return $this->redirectToRoute('app_emprunteur_index', [], Response::HTTP_SEE_OTHER);
    }
}
