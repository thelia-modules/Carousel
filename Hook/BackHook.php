<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carousel\Hook;

use Carousel\Carousel;
use Carousel\Model\Carousel as CarouselModel;
use Carousel\Model\CarouselQuery;
use Thelia\Action\Image as ImageAction;
use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Event\Image\ImageEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Hook\BaseHook;
use Thelia\Tools\URL;

/**
 * Class BackHook.
 *
 * @author Emmanuel Nurit <enurit@openstudio.fr>
 */
class BackHook extends BaseHook
{
    /**
     * Add a new entry in the admin tools menu.
     *
     * should add to event a fragment with fields : id,class,url,title
     */
    public function onMainTopMenuTools(HookRenderBlockEvent $event): void
    {
        $event->add(
            [
                'id' => 'tools_menu_carousel',
                'class' => '',
                'url' => URL::getInstance()->absoluteUrl('/admin/module/Carousel'),
                'title' => $this->trans('Edit your carousel', [], Carousel::DOMAIN_NAME),
            ]
        );
    }

    /**
     * Migration Thelia 3 : les hooks `module.configuration` et `module.config-js` de ce module
     * sont declares dans Config/config.xml (hors perimetre) sur `<hook id="carousel.hook">`,
     * sans attribut "class" : ils tombent donc sur `Thelia\Core\Hook\DefaultHook`, avec la
     * methode heritee `insertTemplate()` et un rendu declaratif fige en base (table
     * `module_hook`, colonne `templates`) sur ".html"/".js" Smarty, qui ne peut jamais basculer
     * sur Twig. On ne peut pas non plus ajouter de nouveau parametre de constructeur ici : ce
     * service (et celui-ci, carousel.hook.back) est instancie par le chargeur XML de Thelia
     * sans autowiring, avec la liste d'arguments exacte declaree en XML (ici : aucune).
     *
     * On reprend donc directement la main sur ces deux hooks en surchargeant `insertTemplate()`
     * sur BackHook (deja cablee, zero argument) et en l'enregistrant via `getSubscribedHooks()`
     * avec le MEME nom de methode ("insertTemplate") que l'ancienne association. Le compilateur
     * de hooks (RegisterHookListenersPass::registerHook()) recherche une ligne module_hook
     * existante par (module, hook, methode) : le nom de methode etant identique, il retrouve
     * les lignes deja existantes (classname="carousel.hook") et se contente de mettre a jour
     * leur classname vers "carousel.hook.back", sans creer de doublon actif. Aucune ecriture
     * manuelle en base n'est necessaire ; c'est le comportement natif du framework quand
     * l'implementation d'un hook change de classe.
     */
    public function insertTemplate(HookRenderEvent $event, string $code): void
    {
        if (str_contains($code, 'module.config-js')) {
            $event->add($this->addJS('assets/js/module-configuration.js'));

            return;
        }

        if (!str_contains($code, 'module.configuration')) {
            parent::insertTemplate($event, $code);

            return;
        }

        $this->renderConfigurationScreen($event);
    }

    private function renderConfigurationScreen(HookRenderEvent $event): void
    {
        $locale = $this->getRequest()?->getLocale() ?? 'en_US';

        $carousels = CarouselQuery::create()->orderByPosition()->find();

        $slides = [];
        $formData = [];

        /** @var CarouselModel $carousel */
        foreach ($carousels as $carousel) {
            $carousel->setLocale($locale);
            $id = $carousel->getId();

            $imageUrl = null;
            $originalImageUrl = null;

            $sourceFilepath = $carousel->getUploadDir().DS.$carousel->getFile();

            if (is_file($sourceFilepath)) {
                $imageEvent = new ImageEvent();
                $imageEvent
                    ->setSourceFilepath($sourceFilepath)
                    ->setCacheSubdirectory('carousel')
                    ->setWidth(550)
                    ->setHeight(200)
                    ->setResizeMode(ImageAction::EXACT_RATIO_WITH_BORDERS)
                ;

                $this->dispatcher->dispatch($imageEvent, TheliaEvents::IMAGE_PROCESS);

                $imageUrl = $imageEvent->getFileUrl();
                $originalImageUrl = $imageEvent->getOriginalFileUrl();
            }

            $startDate = $carousel->getStartDate();
            $endDate = $carousel->getEndDate();

            $slides[] = [
                'id' => $id,
                'image_url' => $imageUrl,
                'original_image_url' => $originalImageUrl,
                'title' => $carousel->getTitle(),
            ];

            $formData['position'.$id] = $carousel->getPosition();
            $formData['alt'.$id] = $carousel->getAlt();
            $formData['group'.$id] = $carousel->getGroup();
            $formData['url'.$id] = $carousel->getUrl();
            $formData['title'.$id] = $carousel->getTitle();
            $formData['chapo'.$id] = $carousel->getChapo();
            $formData['description'.$id] = $carousel->getDescription();
            $formData['postscriptum'.$id] = $carousel->getPostscriptum();
            $formData['disable'.$id] = (bool) $carousel->getDisable();
            $formData['limited'.$id] = (bool) $carousel->getLimited();
            $formData['start_date'.$id] = $startDate;
            $formData['end_date'.$id] = $endDate;
        }

        $event->add($this->render('carousel/module-configuration.html.twig', [
            'slides' => $slides,
            'form_data' => $formData,
            'upload_url' => URL::getInstance()->absoluteUrl('/admin/module/carousel/upload'),
            'update_url' => URL::getInstance()->absoluteUrl('/admin/module/carousel/update'),
            'delete_url' => URL::getInstance()->absoluteUrl('/admin/module/carousel/delete'),
        ]));
    }

    public static function getSubscribedHooks(): array
    {
        return [
            'main.top-menu-tools' => [
                'type' => 'back',
                'method' => 'onMainTopMenuTools',
            ],
            'module.configuration' => [
                'type' => 'back',
                'method' => 'insertTemplate',
            ],
            'module.config-js' => [
                'type' => 'back',
                'method' => 'insertTemplate',
            ],
        ];
    }
}
