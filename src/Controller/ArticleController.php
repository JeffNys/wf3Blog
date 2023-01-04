<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(ArticleRepository $articleRepo): Response
    {
        $articles = $articleRepo->findAll();
        return $this->render('article/index.html.twig', [
            "articles" => $articles,
        ]);
    }

    #[Route('/article/{article}', name: 'app_article_show')]
    public function show(Article $article, CommentRepository $commentRepo): Response
    {
        $comments = $commentRepo->findBy([
            "article" => $article,
        ]);
        return $this->render('article/show.html.twig', [
            "article" => $article,
            "comments" => $comments,
        ]);
    }
}
