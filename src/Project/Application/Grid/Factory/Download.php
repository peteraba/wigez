<?php

namespace Project\Application\Grid\Factory;

use Grid\Factory;
use Grid\Grid;
use Opulence\Routing\Router;
use Project\Domain\Entities\Download as Entity;

class Download extends Base
{
    const GROUP_ID = 'download-id';
    const GROUP_FILE = 'download-file';
    const GROUP_CUSTOMER = 'download-customer';
    const GROUP_DOWNLOADED_AT = 'download-downloaded-at';

    const HEADER_ID = 'Id';
    const HEADER_FILE = 'File';
    const HEADER_CUSTOMER = 'Customer';
    const HEADER_DOWNLOADED_AT = 'Downloaded At';

    const GETTER_ID = 'getId';
    const GETTER_FILE = 'getFile';
    const GETTER_CUSTOMER = 'getCustomer';
    const GETTER_DOWNLOADED_AT = 'getDownloadedAt';

    /** @var array */
    protected $headerAttributes = [];

    /** @var array */
    protected $bodyAttributes = [];

    /** @var Router */
    protected $router;

    /**
     * @param array $pages
     *
     * @return Grid
     */
    public function createGrid(array $pages): Grid
    {
        $headers = [
            static::GROUP_ID            => static::HEADER_ID,
            static::GROUP_FILE          => static::HEADER_FILE,
            static::GROUP_CUSTOMER      => static::HEADER_CUSTOMER,
            static::GROUP_DOWNLOADED_AT => static::HEADER_DOWNLOADED_AT,
        ];
        $getters = [
            static::GROUP_ID            => static::GETTER_ID,
            static::GROUP_FILE          => static::GETTER_FILE,
            static::GROUP_CUSTOMER      => static::GETTER_CUSTOMER,
            static::GROUP_DOWNLOADED_AT => [$this, 'getDownloadedAt'],
        ];

        $grid = Factory::createGrid(
            $pages,
            $getters,
            $headers,
            $this->headerAttributes,
            $this->bodyAttributes,
            $this->tableAttributes,
            $this->gridAttributes
        );

        return $grid;
    }

    /**
     * @param Entity $entity
     *
     * @return string
     */
    public function getDownloadedAt(Entity $entity): string
    {
        return $entity->getDownloadedAt()->format(Entity::DATE_FORMAT);
    }
}


