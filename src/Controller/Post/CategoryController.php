<?php 

namespace App\Controller\Post;  

use App\Entity\Post;
use App\Entity\Category;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\NewCategoryFormType;

// category controller that manage the category page
class CategoryController extends AbstractController
{

    // browse all the categories
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

        // shuffle the categories in a random order
        shuffle($categories);

        // create the form for the new category
        $category = new Category();
        $form = $this->createForm(NewCategoryFormType::class, $category);
        $form->handleRequest($request);

        // if the form is submitted and valid, check if the category already exists
        if ($form->isSubmitted() && $form->isValid()) {

            if ($this->getUser() === null) {
                return $this->redirectToRoute('app_login');
            }

            for ($i = 0; $i < count($categories); $i++) {
                if (strtoupper($categories[$i]->getName()) === strtoupper($form->get('name')->getData())) {
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

    // create a new category
    #[Route('/category/new', name: 'new_category')]
    public function new(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger): Response
    {
        $logger->info('New category page is being accessed');

        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        // create the form for the new category
        $category = new Category();
        $form = $this->createForm(NewCategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $categoryRepository = $doctrine->getRepository(\App\Entity\Category::class);
            $categories = $categoryRepository->findAll();

            for ($i = 0; $i < count($categories); $i++) {
                if (strtoupper($categories[$i]->getName()) === strtoupper($form->get('name')->getData())) {
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

    // show a category and all the posts containing this category
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

        $user = $this->getUser();

        return $this -> render('category/show.category.html.twig', [
            'category' => $category,
            'user' => $user,
        ]);
    }

    // delete a category
    #[Route('/category/{id}/delete', name: 'category_edit')]
    public function edit(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger, int $id): Response
    {
        $logger->info('category delete page is being accessed');

        // check if the user is an admin
        if ($this->getUser() === null) {
            return $this->redirectToRoute('categories_browse');
        }

        if ($this->getUser()->getRoles()[0] !== 'ROLE_ADMIN') {
            return $this->redirectToRoute('categories_browse');
        }

        $categoryRepository = $doctrine->getRepository(Category::class);
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException(
                'No category found for id '. $id
            );
        }

        $entityManager = $doctrine->getManager();

        // remove all categories from the posts
        $posts = $category->getPosts();
        for ($i = 0; $i < count($posts); $i++) {
            $posts[$i]->removeCategory($category); // maybe add a feature that delete the post if no longer a category
            $entityManager->persist($posts[$i]);
        }

        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('categories_browse');
    }
}