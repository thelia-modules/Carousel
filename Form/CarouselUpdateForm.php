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

namespace Carousel\Form;
use Carousel\Carousel;
use Carousel\Model\CarouselQuery;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;


/**
 * Class CarouselUpdateForm
 * @package Carousel\Form
 * @author manuel raynaud <mraynaud@openstudio.fr>
 */
class CarouselUpdateForm extends BaseForm
{

    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     * $this->formBuilder->add("name", "text")
     *   ->add("email", "email", array(
     *           "attr" => array(
     *               "class" => "field"
     *           ),
     *           "label" => "email",
     *           "constraints" => array(
     *               new \Symfony\Component\Validator\Constraints\NotBlank()
     *           )
     *       )
     *   )
     *   ->add('age', 'integer');
     *
     * @return null
     */
    protected function buildForm()
    {
        $formBuilder = $this->formBuilder;

        $carousels = CarouselQuery::create()
            ->orderByPosition()
            ->find();

        $translator = Translator::getInstance();

        $label = $translator->trans('alt text', [], Carousel::DOMAIN_NAME);

        foreach ($carousels as $carousel) {
            $formBuilder->add(
                'alt'.$carousel->getId(),
                'text',
                [
                    'label' => $label,
                    'label_attr' => [
                        'for' => 'carousel_update'.$carousel->getId()
                    ],
                    'required' => false,
                    'data' => $carousel->getAlt()
                ]
            );
        }
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "carousel_update";
    }
}