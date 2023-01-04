<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepo): Response
    {
        $categories = $categoryRepo->findAll();
        return $this->render('category/index.html.twig', [
            "categories" => $categories,
        ]);
    }

    #[Route('/category/{category}', name: 'app_category_show')]
    public function show(Category $category, ArticleRepository $articleRepo): Response
    {
        $articles = $articleRepo->findBy([
            "category" => $category,
        ]);
        return $this->render('category/show.html.twig', [
            "category" => $category,
            "articles" => $articles,
        ]);
    }
}
