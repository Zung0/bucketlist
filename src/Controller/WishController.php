<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\CreateType;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Date;

class WishController extends AbstractController
{
    #[Route('/wish{page}', name: 'app_wish', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function liste(WishRepository $wishRepository, int $page): Response
    {
        if ($page < 1) {
            throw new NotFoundHttpException('impossible');
        }


        $offset = ($page - 1) * 3;
        $wishes = $wishRepository->findWishesSpec($offset);
        $nbWishes = count($wishRepository->findBy(['isPublished' => true]));
        $pagesMax = ceil($nbWishes / 3);

        return $this->render('wish/list.html.twig', [
            'controller_name' => 'WishController',
            'wishes' => $wishes,
            'currentPage' => $page,
            'pagesMax' => $pagesMax

        ]);
    }

    #[Route('/detail/{id}', name: 'app_detail', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function detail(int $id, WishRepository $wishRepository): Response
    {
        $wish = $wishRepository->find($id);
        if (!$wish) {
            throw $this->createNotFoundException('This wish doesn\'t exist');
        }
        return $this->render('wish/details.html.twig', [
            'id' => $id,
            'wish' => $wish
        ]);
    }

    #[Route('/create', name: 'app_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $wish = new Wish();
        $form = $this->createForm(CreateType::class, $wish);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $em->persist($wish);
            $em->flush();
            //dd($wish);
            $this->addFlash('success', 'Enregistrement réussi !');

            return $this->redirectToRoute('app_detail', ['id' => $wish->getId()]);
        }
        return $this->render('wish/create.html.twig', [
            'createForm' => $form
        ]);
    }
    #[Route('/create/{id}', name: 'app_create_id')]
    public function createById(Request $request, EntityManagerInterface $em, Wish $wish): Response
    {

        $form = $this->createForm(CreateType::class, $wish)
                ->setData($wish);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $wish->setDateUpdated(new \DateTime());
            $em->persist($wish);
            $em->flush();
            //dd($wish);
            $this->addFlash('success', 'Enregistrement réussi !');

            return $this->redirectToRoute('app_detail', ['id' => $wish->getId()]);
        }
        return $this->render('wish/create.html.twig', [
            'createForm' => $form
        ]);
    }

    #[Route('/remove/{id}', name: 'app_remove', requirements: ['id' => '\d+'])]
    public function removeWish(Wish $wish, EntityManagerInterface $em): Response
    {

        $em->remove($wish);
        $em->flush();
        $this->addFlash('success', 'Delete réussi !');
        return $this->redirectToRoute('app_main');
    }

}