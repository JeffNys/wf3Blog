<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\PublicCommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    #[Route('/article/{page}', name: 'app_article', defaults: ["page" => 1])]
    public function index(ArticleRepository $articleRepo, int $page): Response
    {
        $itemPerPage = 2;
        $itemsCount = $articleRepo->count([]);
        $pages = [];
        $pageCounter = 0;
        for ($i = 0; $i < $itemsCount; $i += $itemPerPage) {
            $pageCounter++;
            $pages[] = $pageCounter;
        }
        // check if $page is consistent
        if ($page < 1) {
            $page = 1;
        }
        if ($page > $itemsCount / $itemPerPage) {
            $page = $pageCounter;
        }

        $articles = $articleRepo->findBy(
            [],
            [],
            $itemPerPage,
            ($page - 1) * $itemPerPage,
        );

        return $this->render('article/index.html.twig', [
            "articles" => $articles,
            'actualPage' => $page,
            'pages' => $pages,
            'lastPage' => $pageCounter,
        ]);
    }

    #[Route('/article/{article}/show', name: 'app_article_show')]
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

    #[Route('/article/{article}/comment', name: 'app_article_comment')]
    public function comment(Article $article, CommentRepository $commentRepo, Request $request): Response
    {
        $user = $this->getUser();
        $comment = new Comment();
        if ($user) {
            $comment->setAuthor($user->getPseudo());
        }

        $form = $this->createForm(PublicCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentDate = new DateTime();
            $comment->setPublishDate($currentDate)
                ->setArticle($article);
            $commentRepo->save($comment, true);

            return $this->redirectToRoute('app_article_show', [
                "article" => $article->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/comment.html.twig', [
            'comment' => $comment,
            "article" => $article,
            'form' => $form,
        ]);
    }
}
