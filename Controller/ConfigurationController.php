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

namespace Carousel\Controller;
use Carousel\Form\CarouselImageForm;
use Carousel\Form\CarouselUpdateForm;
use Carousel\Model\Carousel;
use Carousel\Model\CarouselQuery;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Event\File\FileCreateOrUpdateEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;


/**
 * Class ConfigurationController
 * @package Carousel\Controller
 * @author manuel raynaud <mraynaud@openstudio.fr>
 */
class ConfigurationController extends BaseAdminController
{

    public function uploadImage()
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, ['carousel'], AccessManager::CREATE)) {
           return $response;
        }

        $request = $this->getRequest();
        $form = new CarouselImageForm($request);
        $error_message = null;
        try {

            $carouselImageForm = $this->validateForm($form);

            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $fileBeingUploaded */
            $fileBeingUploaded = $request->files->get(sprintf('%s[file]', $form->getName()), null, true);

            $fileModel = new Carousel();

            $fileCreateOrUpdateEvent = new FileCreateOrUpdateEvent(1);
            $fileCreateOrUpdateEvent->setModel($fileModel);
            $fileCreateOrUpdateEvent->setUploadedFile($fileBeingUploaded);

            $this->dispatch(
                TheliaEvents::IMAGE_SAVE,
                $fileCreateOrUpdateEvent
            );

            $response =  $this->generateRedirect();

        } catch(FormValidationException $e) {
            $error_message = $this->createStandardFormValidationErrorMessage($e);
        }

        if (null !== $error_message) {
            $this->setupFormErrorContext(
                'carousel upload',
                $error_message,
                $form
            );

            $response = $this->render(
                "module-configure",
                [
                    'module_code' => 'Carousel'
                ]
            );
        }

        return $response;
    }

    public function updateAction()
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, ['carousel'], AccessManager::UPDATE)) {
            return $response;
        }

        $request = $this->getRequest();

        $form = new CarouselUpdateForm($request);
        $error_message = null;
        try {
            $updateForm = $this->validateForm($form);

            $carousels = CarouselQuery::create()->findAllByPosition();

            foreach ($carousels as $carousel) {
                $carousel->setAlt(
                    $updateForm->get(sprintf('alt%d', $carousel->getId()))->getData()
                )->save();
            }
            $response =  $this->generateRedirect();

        } catch(FormValidationException $e) {
            $error_message = $this->createStandardFormValidationErrorMessage($e);
        }

        if (null !== $error_message) {
            $this->setupFormErrorContext(
                'carousel upload',
                $error_message,
                $form
            );

            $response = $this->render(
                "module-configure",
                [
                    'module_code' => 'Carousel'
                ]
            );
        }

        return $response;

    }

    public function deleteAction()
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, ['carousel'], AccessManager::DELETE)) {
            return $response;
        }

        $imageId = $this->getRequest()->request->get('image_id');

        if ($imageId != "") {
            $carousel = CarouselQuery::create()->findPk($imageId);

            if (null !== $carousel) {
                $carousel->delete();
            }
        }

        return $this->generateRedirect();
    }

    protected function generateRedirect()
    {
        return RedirectResponse::create(URL::getInstance()->absoluteUrl('/admin/module/Carousel'));
    }
} 