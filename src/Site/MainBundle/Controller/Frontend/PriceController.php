<?php

namespace Site\MainBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PriceController extends Controller
{
    public function indexAction()
    {
        $repository_page = $this->getDoctrine()->getRepository('SiteMainBundle:Page');

        $page = $repository_page->findOneBySlug('uslughi-i-tsieny'); 

        if(!$page) {
            throw $this->createNotFoundException($this->get('translator')->trans('Страница не найдена'));
        }

        return $this->render('SiteMainBundle:Frontend/Price:index.html.twig', array(
            'page' => $page
        ));
    }
}
