<?php

namespace Site\MainBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Site\MainBundle\Form\FeedbackType;
use Site\MainBundle\Form\Feedback;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{
    public function indexAction($slug = null)
    {
        $repository_page = $this->getDoctrine()->getRepository('SiteMainBundle:Page');
        $repository_project = $this->getDoctrine()->getRepository('SiteMainBundle:Project');
        $repository_service = $this->getDoctrine()->getRepository('SiteMainBundle:Service');
        $repository_sliders = $this->getDoctrine()->getRepository('SiteMainBundle:Background');
        $repository_partners = $this->getDoctrine()->getRepository('SiteMainBundle:Partners');

        if(is_null($slug)){
            $page = $repository_page->findOneBySlug('glavnaia');
        }else{
            $page = $repository_page->findOneBySlug($slug);

            if(!$page) {
                throw $this->createNotFoundException($this->get('translator')->trans('Страница не найдена'));
            }
        }

        $projects = $repository_project->findBy(array('onMain' => true, 'onShow' => true));
        $services = $repository_service->findAll();
        $partners = $repository_partners->findAll();
        $sliders = $repository_sliders->findBy(array('main' => true));
        $form = $this->createCreateForm(new Feedback());

        return $this->render('SiteMainBundle:Frontend/Main:index.html.twig', array(
            'page' => $page,
            'projects' => $projects,
            'services' => $services,
            'partners' => $partners,
            'sliders' => $sliders,
            'form' => $form->createView()
        ));
    }
    public function contactsAction()
    {
        $repository_page = $this->getDoctrine()->getRepository('SiteMainBundle:Page');

        $page = $repository_page->findOneBySlug('kontakty');

        $form = $this->createCreateForm(new Feedback());

        return $this->render('SiteMainBundle:Frontend/Main:contacts.html.twig', array(
            'page' => $page,
            'form' => $form->createView()
        ));
    }

    public function feedbackAction(Request $request){
        $feedback = new Feedback();
        $form = $this->createForm(new FeedbackType(), $feedback);

        $form->handleRequest($request);

        if($form->isValid()){
            $swift = \Swift_Message::newInstance()
                ->setSubject('Lab-developers.com (Новое письмо)')
                ->setFrom(array($this->container->getParameter('email_from') => "Новое письмо с сайта"))
                ->setTo($this->container->getParameter('emails_admin'))
                ->setBody(
                    $this->renderView(
                        'SiteMainBundle:Frontend/Feedback:message.html.twig',
                        array(
                            'form' => $feedback
                        )
                    )
                    , 'text/html'
                );
            $this->get('mailer')->send($swift);

            return new JsonResponse([
                'status' => 'OK',
                'message' => 'Ваше письмо успешно отправлено. Мы скоро с вами свяжемся!'
            ]);
        } else {
            if ($form->count()) {
                foreach ($form as $child) {
                    if (!$child->isValid()) {
                        $errors[$child->getName()]['status'] = 'ERROR';
                    } else {
                        $errors[$child->getName()]['status'] = "OK";
                    }
                }
            }

            return new JsonResponse(array_merge(['status' => 'ERROR'], $errors));
        }
    }

    public function sitemapAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $urls = array();
        $hostname = $request->getSchemeAndHttpHost();
 
        // add static urls
        $urls[] = array('loc' => $this->generateUrl('frontend_homepage'));
        $urls[] = array('loc' => $this->generateUrl('frontend_work_index'));
        $urls[] = array('loc' => $this->generateUrl('frontend_news_all'));
        $urls[] = array('loc' => $this->generateUrl('frontend_page_contacts'));
        $urls[] = array('loc' => $this->generateUrl('frontend_page_about'));
        $urls[] = array('loc' => $this->generateUrl('frontend_page_price'));
         
        // // add static urls with optional tags
        // $urls[] = array('loc' => $this->generateUrl('fos_user_security_login'), 'changefreq' => 'monthly', 'priority' => '1.0');
        // $urls[] = array('loc' => $this->generateUrl('cookie_policy'), 'lastmod' => '2018-01-01');
         
        // // add dynamic urls, like blog posts from your DB
        // foreach ($em->getRepository('BlogBundle:post')->findAll() as $post) {
        //     $urls[] = array(
        //         'loc' => $this->generateUrl('blog_single_post', array('post_slug' => $post->getPostSlug()))
        //     );
        // }
 
        // // add image urls
        // $products = $em->getRepository('AppBundle:products')->findAll();
        // foreach ($products as $item) {
        //     $images = array(
        //         'loc' => $item->getImagePath(), // URL to image
        //         'title' => $item->getTitle()    // Optional, text describing the image
        //     );
 
        //     $urls[] = array(
        //         'loc' => $this->generateUrl('single_product', array('slug' => $item->getProductSlug())),
        //         'image' => $images              // set the images for this product url
        //     );
        // }
       
 
        // return response in XML format
        $response = new Response(
            $this->renderView('SiteMainBundle:Frontend/Main:sitemap.html.twig', array( 'urls' => $urls,
                'hostname' => $hostname)),
            200
        );
        $response->headers->set('Content-Type', 'text/xml');
 
        return $response;
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
