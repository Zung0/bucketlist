<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\CreateType;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
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
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $wish = new Wish();
        $form = $this->createForm(CreateType::class, $wish);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form ->isValid()) {

            if($form->get('image_file')->getData()instanceof UploadedFile){
                $imageFile = $form->get('image_file')->getData();
                $fileName = $slugger->slug($wish->getName()).'-'.uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move('images', $fileName);
                $wish->setImage($fileName);
            }
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

    #[Route('/create/{id}', name: 'app_create_id', requirements: ['id' => '\d+' ])]
    public function createById(Request $request, EntityManagerInterface $em, Wish $wish, SluggerInterface $slugger): Response
    {

        $form = $this->createForm(CreateType::class, $wish);
          //  ->setData($wish) inutile étant donné que tu donnes déjà $wish dans createForm.

        $form->handleRequest($request);

        if ($form->isSubmitted()&& $form ->isValid()) {

            if ($form->get('delete_image')->getData()){
              $wish->deleteImage();
              $wish->setImage(null);
          }

            if($form->get('image_file')->getData()instanceof UploadedFile){
                $imageFile = $form->get('image_file')->getData();
                $fileName = $slugger->slug($wish->getName()).'-'.uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move('images', $fileName);

                if($wish->getImage()&& file_exists('images'.$wish->getImage())){
                    //unlink permet de détruire le fichier initial.
                    unlink('images'.$wish->getImage());
                }
                $wish->setImage($fileName);
            }
            $em->persist($wish);
            $em->flush();
            //dd($wish);
            $this->addFlash('success', 'Modification réussi !');

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
