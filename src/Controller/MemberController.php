<?php

namespace App\Controller;

use App\Entity\Member;
use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/member')]
final class MemberController extends AbstractController
{
    #[Route('/me', name: 'app_me', methods: ['GET'])]
    public function me(): Response
    {
        /** @var Member|null $member */
        $member = $this->getUser();
        if (!$member) {
            return $this->redirectToRoute('app_login');
        }
        // suppose que tu as déjà une route app_member_show
        return $this->redirectToRoute('app_member_show', ['id' => $member->getId()]);
    }
    
    #[Route(name: 'app_member_index', methods: ['GET'])]
    public function index(MemberRepository $repo): Response
    {
        // Liste temporaire (disparaîtra plus tard)
        return $this->render('member/index.html.twig', [
            'members' => $repo->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_member_show', methods: ['GET'])]
    public function show(Member $member): Response
    {
        return $this->render('member/show.html.twig', [
            'member' => $member,
        ]);
    }
}
