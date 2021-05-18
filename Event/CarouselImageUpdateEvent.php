<?php

namespace Carousel\Event;

use Symfony\Component\Form\Form;
use Thelia\Core\Event\ActionEvent;

class CarouselImageUpdateEvent extends ActionEvent
{
    public const CAROUSEL_IMAGE_UPDATE = 'carousel.image.update';

    /** @var Form */
    protected $validatedForm;

    public function __construct(Form $validatedForm)
    {
        $this->validatedForm = $validatedForm;
    }

    public function getValidatedForm()
    {
        return $this->validatedForm;
    }
}
