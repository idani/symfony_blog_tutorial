<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Form\Type\PostType;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->findAll();
        return $this->render('blog/index.html.twig', [
            'posts' => $post,
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show", requirements={"id"="\d+"})
     *
     * @param [type] $id
     * @return void
     */
    public function show($id)
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
        if (!$post) {
            throw $this->createNotFoundException("The post does not exists");
        }

        return $this->render('blog/show.html.twig', ['post' => $post]);
    }

    /**
     * @Route("/blog/new", name="blog_new")
     *
     * @return void
     */
    public function new(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setCreatedAt(new \DateTime());
            $post->setUpdatedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('blog');
        }

        return $this->render('blog/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/blog/{id}/delete", name="blog_delete", requirements={"id"="\d+"})
     *
     * @param [type] $id
     * @return void
     */
    public function delete($id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($id);
        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id ' . $id
            );
        }


        // 削除処理
        $em->remove($post);
        $em->flush();

        return $this->redirectToRoute('blog');
    }

    /**
     * @Route("/blog/{id}/edit", name="blog_edit", requirements={"id"="\d+"})
     *
     * @param [type] $id
     * @return void
     */
    public function edit($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($id);
        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id ' . $id
            );
        }

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUpdatedAt(new \DateTime());
            $em->flush();

            return $this->redirectToRoute('blog');
        }

        return $this->render('blog/new.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }
}
