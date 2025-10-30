<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\Manga;
use App\Entity\Vitrine;
use App\Form\VitrineType;
use App\Repository\VitrineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/vitrine')]
final class VitrineController extends AbstractController
{
    #[Route(name: 'app_vitrine_index', methods: ['GET'])]
    public function index(VitrineRepository $repo): Response
    {
        // Uniquement les vitrines publiques
        $vitrines = $repo->findBy(['publiee' => true], ['id' => 'ASC']);

        return $this->render('vitrine/index.html.twig', [
            'vitrines' => $vitrines,
        ]);
    }

    #[Route('/new/{member_id}', name: 'app_vitrine_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        #[MapEntity(id: 'member_id')] Member $member
    ): Response {
        // Optionnel : restreindre la création aux propriétaires ou admins
        $user = $this->getUser();
        $isOwner = $user instanceof Member && $user->getId() === $member->getId();
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        if (!$isOwner && !$isAdmin) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas créer une vitrine pour un autre membre.");
        }

        $vitrine = new Vitrine();
        $vitrine->setCreateur($member);

        $form = $this->createForm(VitrineType::class, $vitrine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($vitrine);
            $em->flush();

            return $this->redirectToRoute('app_member_show', [
                'id' => $member->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vitrine/new.html.twig', [
            'vitrine' => $vitrine,
            'form'    => $form,
            'member'  => $member,
        ]);
    }

    #[Route('/{id}', name: 'app_vitrine_show', methods: ['GET'])]
    public function show(Vitrine $vitrine): Response
    {
        $user = $this->getUser();
        $isOwner = $user instanceof Member && $vitrine->getCreateur() === $user;
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        if (!$vitrine->isPubliee() && !$isOwner && !$isAdmin) {
            throw $this->createAccessDeniedException("Cette vitrine est privée.");
        }

        return $this->render('vitrine/show.html.twig', [
            'vitrine' => $vitrine,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vitrine_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vitrine $vitrine, EntityManagerInterface $em): Response
    {
        // Sécurité : seul le créateur ou un admin peut éditer
        $user = $this->getUser();
        $isOwner = $user instanceof Member && $vitrine->getCreateur() === $user;
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        if (!$isOwner && !$isAdmin) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas modifier cette vitrine.");
        }

        $form = $this->createForm(VitrineType::class, $vitrine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_member_show', [
                'id' => $vitrine->getCreateur()->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vitrine/edit.html.twig', [
            'vitrine' => $vitrine,
            'form'    => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vitrine_delete', methods: ['POST'])]
    public function delete(Request $request, Vitrine $vitrine, EntityManagerInterface $em): Response
    {
        // Sécurité : seul le créateur ou un admin peut supprimer
        $user = $this->getUser();
        $isOwner = $user instanceof Member && $vitrine->getCreateur() === $user;
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        if (!$isOwner && !$isAdmin) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas supprimer cette vitrine.");
        }

        if ($this->isCsrfTokenValid('delete'.$vitrine->getId(), $request->getPayload()->getString('_token'))) {
            $memberId = $vitrine->getCreateur()->getId(); // récupérer avant suppression
            $em->remove($vitrine);
            $em->flush();

            return $this->redirectToRoute('app_member_show', [
                'id' => $memberId,
            ], Response::HTTP_SEE_OTHER);
        }

        // En cas d'échec CSRF, on revient à la fiche vitrine
        return $this->redirectToRoute('app_vitrine_show', [
            'id' => $vitrine->getId(),
        ], Response::HTTP_SEE_OTHER);
    }


    #[Route(
        '/{vitrine_id}/manga/{manga_id}',
        name: 'app_vitrine_manga_show',
        requirements: ['vitrine_id' => '\d+', 'manga_id' => '\d+'],
        methods: ['GET']
    )]
    public function mangaShow(
        #[MapEntity(id: 'vitrine_id')] Vitrine $vitrine,
        #[MapEntity(id: 'manga_id')] Manga $manga
    ): Response {
        // 1) le manga doit appartenir à la vitrine
        if (!$vitrine->getMangas()->contains($manga)) {
            throw $this->createNotFoundException("Ce manga n'appartient pas à cette vitrine.");
        }

        $user = $this->getUser();
        $isOwner = $user instanceof Member && $vitrine->getCreateur() === $user;
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        if (!$vitrine->isPubliee() && !$isOwner && !$isAdmin) {
            throw $this->createAccessDeniedException("Cette vitrine n'est pas publique.");
        }

        return $this->render('vitrine/mangashow.html.twig', [
            'manga'   => $manga,
            'vitrine' => $vitrine,
        ]);
    }
}
