<?php

namespace AppBundle\Controller;

use AppBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * Default home arrival action where all image posts
     * are displaayed in one thread. This route also allows
     * posting new images using the POST method.
     * 
     * @Route("/", name="forum")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function forumAction(Request $request)
    {
        $forumservice = $this->get('app.forumservice');
        $post = $this->get('app.postfactory')->create();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $forumservice->uploadPost($post);
            $this->addFlash('success', 'image.saved');
        }

        $forumservice->increaseViews();
        $stats = $forumservice->getStats();
        $posts = $forumservice->getAllPosts();

        return $this->render('forum.html.twig', array_merge([
            'form' => $form->createView(),
            'posts' => $posts,
            'uploadDir' => $this->getParameter('upload_dir')
        ], $stats));
    }
    
    /**
     * Accessing to this route using the GET method allow clients
     * to retrieve a report of all the hosted image images
     * describing each image with its title and file name
     * 
     * @Route("/export", name="export")
     * @Method({"GET"})
     * @param Request $request
     * @return Response
     */
    public function exportAction(Request $request)
    {
        $forumservice = $this->get('app.forumservice');
        $posts = $forumservice->getAllPosts();
        $response = $this->render('forum.csv.html.twig', array('posts' => $posts));
        $filename = $this->get('translator')->trans('images');
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename=$filename.csv");
        
        return $response;
    }
}
