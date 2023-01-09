<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
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
        $itemPerPage = 3;
        $adjacents = 2;
        $itemsCount = $articleRepo->count([]);
        $totalPages =  (int)floor($itemsCount / $itemPerPage);
        // check if $page is consistent
        if ($page < 1) {
            $page = 1;
        }
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $pivot = 0;
        $startFar = true;
        $endFar = true;
        $pages = [];

        if ($totalPages < 2 * $adjacents + 2) {
            // if there is not enought page for respect adjacents
            for ($i = 1; $i < $totalPages + 1; $i++) {
                $pages[] = $i;
            }
        } else {
            if ($page < $adjacents + 2) {
                // if we are not so far to start
                $pivot = $adjacents + 1;
                $startFar = false;
            } elseif ($page > $totalPages - ($adjacents + 1)) {
                // if we are not so far to end
                $pivot = $totalPages - $adjacents;
                $endFar = false;
            } else {
                $pivot = $page;
            }

            $pages[] = $pivot;
            for ($i = 1; $i < $adjacents - 1; $i++) {
                array_push($pages, $pivot + $i);
                array_unshift($pages, $pivot - $i);
            }
            if ($startFar) {
                array_unshift($pages, "...");
            } else {
                array_unshift($pages, 2);
            }

            if ($endFar) {
                array_push($pages, "...");
            } else {
                array_push($pages, $totalPages - 1);
            }

            array_push($pages, $totalPages);
            array_unshift($pages, 1);
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
            'lastPage' => $totalPages,
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
        /**
         * @var User
         */
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
