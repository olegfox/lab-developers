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
            } else {
                /**
                 * Проверка регистра slug
                 * и перенаправление на странице с правильным регистром
                 * в url
                 */
                if ($page->getSlug() !== $slug) {
                    return $this->redirect($this->generateUrl('frontend_page_index', array('slug' => $page->getSlug())), 301);
                }
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
        $breadcrumbs = $this->get("white_october_breadcrumbs");

        $breadcrumbs->addItem("Главная", $this->get("router")->generate("frontend_homepage"));

        $repository_page = $this->getDoctrine()->getRepository('SiteMainBundle:Page');

        $page = $repository_page->findOneBySlug('kontakty');

        $breadcrumbs->addItem($page->getTitle());

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
        $urls = array();
        $hostname = $request->getSchemeAndHttpHost();
 
        $urls[] = array('loc' => $this->generateUrl('frontend_homepage'));
        $urls[] = array('loc' => $this->generateUrl('frontend_news_all'));
        $urls[] = array('loc' => $this->generateUrl('frontend_page_about'));
        $urls[] = array('loc' => $this->generateUrl('frontend_page_price'));
        $urls[] = array('loc' => $this->generateUrl('frontend_work_index'));
        $urls[] = array('loc' => $this->generateUrl('frontend_page_contacts'));
         
        // Новости 
        $repository_news = $this->getDoctrine()->getRepository('SiteMainBundle:News');
        $news = $repository_news->findAll();

        foreach ($news as $post) {
            $urls[] = array(
                'loc' => $this->generateUrl('frontend_news_one', array('slug' => $post->getSlug()))
            );
        }

        // Услуги
        $repository_service = $this->getDoctrine()->getRepository('SiteMainBundle:Service');
        $services = $repository_service->findAll();

        foreach ($services as $service) {
            $urls[] = array(
                'loc' => $this->generateUrl('frontend_service_one', array('slug' => $service->getSlug()))
            );
        }
 
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
