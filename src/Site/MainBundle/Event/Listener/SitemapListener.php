<?php
namespace Site\MainBundle\Event\Listener;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class SitemapListener implements SitemapListenerInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function populateSitemap(SitemapPopulateEvent $event)
    {
        $section = $event->getSection();
        if (is_null($section) || $section == 'default') {
            //get absolute homepage url
            $urls = array(
                'frontend_homepage' => $this->router->generate('frontend_homepage', array(), UrlGeneratorInterface::ABSOLUTE_URL),
                'frontend_feedback' => $this->router->generate('frontend_feedback', array(), UrlGeneratorInterface::ABSOLUTE_URL),
                'frontend_work_index' => $this->router->generate('frontend_work_index', array(), UrlGeneratorInterface::ABSOLUTE_URL),
                'frontend_page_contacts' => $this->router->generate('frontend_page_contacts', array(), UrlGeneratorInterface::ABSOLUTE_URL)
            );

            foreach($urls as $url) {
                $event->getGenerator()->addUrl(
                    new UrlConcrete(
                        $url,
                        new \DateTime(),
                        UrlConcrete::CHANGEFREQ_HOURLY,
                        1
                    ),
                    'default'
                );
            }

        }
    }
}