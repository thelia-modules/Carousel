<?php

namespace Carousel\Model;

use Carousel\Form\CarouselImageForm;
use Carousel\Model\Base\Carousel as BaseCarousel;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Files\FileModelInterface;
use Thelia\Files\FileModelParentInterface;
use Thelia\Form\BaseForm;

class Carousel extends BaseCarousel implements FileModelInterface
{

    /**
     * Set file parent id
     *
     * @param int $parentId parent id
     *
     * @return $this
     */
    public function setParentId($parentId)
    {
        return $this;
    }

    /**
     * Get file parent id
     *
     * @return int parent id
     */
    public function getParentId()
    {
        return $this->getId();
    }

    /**
     * @return FileModelParentInterface the parent file model
     */
    public function getParentFileModel()
    {
        return new static;
    }

    /**
     * Get the ID of the form used to change this object information
     *
     * @return BaseForm the form
     */
    public function getUpdateFormId()
    {
        return 'carousel.image';
    }

    /**
     * Get the form instance used to change this object information
     *
     * @param Request $request the current request
     *
     * @return BaseForm the form
     */
    public function getUpdateFormInstance(Request $request)
    {
        return new CarouselImageForm($request);
    }

    /**
     * @return string the path to the upload directory where files are stored, without final slash
     */
    public function getUploadDir()
    {
        $carousel = new \Carousel\Carousel();
        return $carousel->getUploadDir();
    }

    /**
     * @param int $objectId the object ID
     *
     * @return string the URL to redirect to after update from the back-office
     */
    public function getRedirectionUrl()
    {
        return '/admin/module/Carousel';
    }

    /**
     * Get the Query instance for this object
     *
     * @return ModelCriteria
     */
    public function getQueryInstance()
    {
        return CarouselQuery::create();
    }

    /**
     * Set the current title
     *
     * @param string $title the title in the current locale
     */
    public function setTitle($title)
    {
        // TODO: Implement setTitle() method.
    }

    /**
     * Get the current title
     *
     * @param string $title the title in the current locale
     * @return FileModelInterface
     */
    public function getTitle()
    {
        // TODO: Implement getTitle() method.
    }

    /**
     * Set the chapo
     *
     * @param string $chapo the chapo in the current locale
     * @return FileModelInterface
     */
    public function setChapo($chapo)
    {
        // TODO: Implement setChapo() method.
    }

    /**
     * Set the description
     *
     * @param string $description the description in the current locale
     * @return FileModelInterface
     */
    public function setDescription($description)
    {
        // TODO: Implement setDescription() method.
    }

    /**
     * Set the postscriptum
     *
     * @param string $postscriptum the postscriptum in the current locale
     * @return FileModelInterface
     */
    public function setPostscriptum($postscriptum)
    {
        // TODO: Implement setPostscriptum() method.
    }

    /**
     * Set the current locale
     *
     * @param string $locale the locale string
     * @return FileModelInterface
     */
    public function setLocale($locale)
    {
        // TODO: Implement setLocale() method.
    }
}
