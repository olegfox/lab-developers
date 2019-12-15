<?php

namespace Site\MainBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Site\MainBundle\Form\FeedbackType;
use Site\MainBundle\Form\Feedback;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class WorkController extends Controller
{
    public function indexAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");

        $breadcrumbs->addItem("Главная", $this->get("router")->generate("frontend_homepage"));

        $repository_page = $this->getDoctrine()->getRepository('SiteMainBundle:Page');
        $repository_project = $this->getDoctrine()->getRepository('SiteMainBundle:Project');

        $page = $repository_page->findOneBySlug('nashi-raboty');

        $breadcrumbs->addItem($page->getTitle());

        $projects = $repository_project->findBy(array('onShow' => true));

        return $this->render('SiteMainBundle:Frontend/Work:index.html.twig', array(
            'page' => $page,
            'projects' => $projects
        ));
    }
}
