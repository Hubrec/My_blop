<?php 

namespace App\Controller\User;  

use App\Entity\Post;
use App\Entity\Category;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\NewCategoryFormType;

class CategoryController extends AbstractController
{

    #[Route('/categories', name: 'categories_browse')]
    public function index(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger): Response
    {
        $logger->info('categories page is being accessed');

        $categoryRepository = $doctrine->getRepository(\App\Entity\Category::class);
        $categories = $categoryRepository->findAll();

        if (!$categories) {
            throw $this->createNotFoundException(
                'No category found for id '. $id
            );
        }

        shuffle($categories);

        $category = new Category();
        $form = $this->createForm(NewCategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($this->getUser() === null) {
                return $this->redirectToRoute('app_login');
            }

            for ($i = 0; $i < count($categories); $i++) {
                if ($categories[$i]->getName() === $form->get('name')->getData()) {
                    return $this->redirectToRoute('categories_browse');
                }
            }

            $category->setName($form->get('name')->getData());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('categories_browse');
        }

        return $this -> render('category/index.category.html.twig', [
            'categories' => $categories,
            'newCategoryForm' => $form->createView(),
        ]);
    }

    #[Route('/category/new', name: 'new_category')]
    public function new(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger): Response
    {
        $logger->info('New category page is being accessed');

        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $category = new Category();
        $form = $this->createForm(NewCategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            for ($i = 0; $i < count($categories); $i++) {
                if ($categories[$i]->getName() === $form->get('name')->getData()) {
                    return $this->redirectToRoute('categories_browse');
                }
            }

            $category->setName($form->get('name')->getData());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('categories_browse');
        }

        return $this -> render('category/new.category.html.twig', [
            'newCategoryForm' => $form->createView(),
        ]);
    }

    #[Route('/category/{id}', name: 'category_browse')]
    public function show(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger, int $id): Response
    {
        $logger->info('category page is being accessed');

        $categoryRepository = $doctrine->getRepository(Category::class);
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException(
                'No category found for id '. $id
            );
        }

        return $this -> render('category/show.category.html.twig', [
            'category' => $category,
        ]);
    }
}