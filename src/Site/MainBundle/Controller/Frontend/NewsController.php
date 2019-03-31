<?php

namespace Site\MainBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Site\MainBundle\Form\FeedbackType;
use Site\MainBundle\Form\Feedback;
use Symfony\Component\HttpFoundation\Response;

class NewsController extends Controller
{
    public function indexAction()
    {
        $repository_news = $this->getDoctrine()->getRepository('SiteMainBundle:News');
        $repository_page = $this->getDoctrine()->getRepository('SiteMainBundle:Page');
        $news = $repository_news->findAll();
        $page = $repository_page->findOneBySlug('blog');
        return $this->render('SiteMainBundle:Frontend/News:index.html.twig', array(
            'news' => $news,
            'page' => $page
        ));
    }

    /**
     * Get one news
     *
     * @param $slug
     * @return Response
     */
    public function oneAction($slug)
    {
        $repository_news = $this->getDoctrine()->getRepository('SiteMainBundle:News');
        $newsOne = $repository_news->findOneBySlug($slug);

        return $this->render('SiteMainBundle:Frontend/News:one.html.twig', array(
            'newsOne' => $newsOne
        ));
    }

    /**
     * Get one news ajax
     *
     * @param $slug
     * @return Response
     */
    public function ajaxOneAction($slug)
    {
        $repository_news = $this->getDoctrine()->getRepository('SiteMainBundle:News');

        $newsOne = $repository_news->findOneBySlug($slug);

        return new Response($this->renderView('SiteMainBundle:Frontend/News:one.html.twig', array(
            'newsOne' => $newsOne
        )));
    }

    /**
     * Создание формы обратной связи
     *
     * @param Feedback $entity
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateForm(Feedback $entity)
    {
        $form = $this->createForm(new FeedbackType(), $entity);

        return $form;
    }
}
