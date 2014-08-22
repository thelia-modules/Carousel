<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Carousel\Loop;
use Carousel\Carousel;
use Carousel\Model\CarouselQuery;
use Thelia\Core\Event\Image\ImageEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Core\Template\Loop\Image;


/**
 * Class CarouselLoop
 * @package Carousel\Loop
 * @author manuel raynaud <mraynaud@openstudio.fr>
 */
class CarouselLoop extends Image
{


    /**
     * Definition of loop arguments
     *
     * example :
     *
     * public function getArgDefinitions()
     * {
     *  return new ArgumentCollection(
     *
     *       Argument::createIntListTypeArgument('id'),
     *           new Argument(
     *           'ref',
     *           new TypeCollection(
     *               new Type\AlphaNumStringListType()
     *           )
     *       ),
     *       Argument::createIntListTypeArgument('category'),
     *       Argument::createBooleanTypeArgument('new'),
     *       ...
     *   );
     * }
     *
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(

        );
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        $carousel = new Carousel();
        /** @var \Carousel\Model\Carousel $carousel */
        foreach ($loopResult->getResultDataCollection() as $carousel) {
            $loopResultRow = new LoopResultRow($carousel);

            $event = new ImageEvent();
            $event->setResizeMode(\Thelia\Action\Image::KEEP_IMAGE_RATIO)
                ->setSourceFilepath($carousel->getUploadDir() . DS . $carousel->getFile())
                ->setCacheSubdirectory('carousel');

            // Dispatch image processing event
            $this->dispatcher->dispatch(TheliaEvents::IMAGE_PROCESS, $event);

            $loopResultRow
                ->set('ID', $carousel->getId())
                ->set('FILE', $event->getOriginalFileUrl())
                ->set('ALT', $carousel->getAlt())
            ;

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $search = CarouselQuery::create()
            ->orderByPosition()
        ;

        return $search;
    }
}