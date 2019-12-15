<?php

namespace Site\MainBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ServiceController extends Controller
{
    public function indexAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");

        $breadcrumbs->addItem("Главная", $this->get("router")->generate("frontend_homepage"));

        $repository_page = $this->getDoctrine()->getRepository('SiteMainBundle:Page');

        $page = $repository_page->findOneBySlug('uslugi'); 

        $breadcrumbs->addItem($page->getTitle());

        if(!$page) {
            throw $this->createNotFoundException($this->get('translator')->trans('Страница не найдена'));
        }

        return $this->render('SiteMainBundle:Frontend/Service:index.html.twig', array(
            'page' => $page
        ));
    }

    /**
     * Get one service
     *
     * @param $slug
     * @return Response
     */
    public function oneAction($slug)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");

        $breadcrumbs->addItem("Главная", $this->get("router")->generate("frontend_homepage"));

        $breadcrumbs->addItem("Услуги", $this->get("router")->generate("frontend_page_price"));

        $repository_service = $this->getDoctrine()->getRepository('SiteMainBundle:Service');
        $serviceOne = $repository_service->findOneBySlug($slug);

        $breadcrumbs->addItem($serviceOne->getTitle());

        return $this->render('SiteMainBundle:Frontend/Service:one.html.twig', array(
            'serviceOne' => $serviceOne
        ));
    }
}
