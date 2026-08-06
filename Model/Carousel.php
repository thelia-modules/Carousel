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

namespace Carousel\Model;

use Carousel\Model\Base\Carousel as BaseCarousel;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Thelia\Core\File\FileModelInterface;
use Thelia\Core\File\FileModelParentInterface;
use Thelia\Form\BaseForm;

class Carousel extends BaseCarousel implements FileModelInterface
{
    /**
     * Migration Thelia 3 : FileModelInterface::getFile() est desormais typee
     * `string`, alors que la colonne `file` est nullable — Propel genere donc
     * `?string` dans la classe de base et l'implementation devient incompatible.
     * On restreint le type de retour ici (covariance legale) plutot que de rendre
     * la colonne obligatoire, ce qui modifierait le schema de la table.
     */
    public function getFile(): string
    {
        return (string) parent::getFile();
    }

    public function preDelete(?ConnectionInterface $con = null): bool {
        $carousel = new \Carousel\Carousel();

        $fs = new Filesystem();

        try {
            $fs->remove($carousel->getUploadDir().DS.$this->getFile());

            return true;
        } catch (IOException $e) {
            return false;
        }
    }

    /**
     * Set file parent id.
     *
     * @param int $parentId parent id
     *
     * @return $this
     */
    public function setParentId($parentId): static {
        return $this;
    }

    /**
     * Get file parent id.
     *
     * @return int parent id
     */
    public function getParentId(): int {
        return $this->getId();
    }

    /**
     * @return FileModelParentInterface the parent file model
     */
    public function getParentFileModel(): \Thelia\Core\File\FileModelParentInterface {
        return new static();
    }

    /**
     * Get the ID of the form used to change this object information.
     *
     * @return BaseForm the form
     */
    public function getUpdateFormId(): string {
        return 'carousel.image';
    }

    /**
     * @return string the path to the upload directory where files are stored, without final slash
     */
    public function getUploadDir(): string {
        $carousel = new \Carousel\Carousel();

        return $carousel->getUploadDir();
    }

    /**
     * @return string the URL to redirect to after update from the back-office
     */
    public function getRedirectionUrl(): string {
        return '/admin/module/Carousel';
    }

    /**
     * Get the Query instance for this object.
     *
     * @return ModelCriteria
     */
    public function getQueryInstance(): \Propel\Runtime\ActiveQuery\ModelCriteria {
        return CarouselQuery::create();
    }

    /**
     * @param bool $visible true if the file is visible, false otherwise
     *
     * @return FileModelInterface
     */
    // Migration Thelia 3 : le parametre est desormais type dans l'interface
    // (?int, car la colonne est un TINYINT nullable et non un booleen).
    public function setVisible(?int $visible = null): static
    {
        // Not implemented

        return $this;
    }
}
