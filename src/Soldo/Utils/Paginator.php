<?php

namespace Soldo\Utils;

/**
 * Class Paginator
 *
 * @package Soldo\Utils
 */
class Paginator
{

    /**
     * Define max allowed items per page according to the API
     */
    const MAX_ALLOWED_ITEMS_PER_PAGE = 50;

    /**
     * Define the keys of the query param
     */
    const PAGE_KEY = 'p';
    const PER_PAGE_KEY = 's';

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $perPage;

    /**
     * Paginator constructor.
     * @param int $page
     * @param int $perPage
     * @param string $orderDirection
     * @param string $orderedProperties
     */
    public function __construct($page, $perPage, $orderDirection = null, $orderedProperties = null)
    {
        if (is_int($page) === false) {
            throw new \InvalidArgumentException(
                'Error trying to create the paginator '
                . '$page must be a positive integer'
            );
        }

        if (is_int($perPage) === false) {
            throw new \InvalidArgumentException(
                'Error trying to create the paginator '
                . '$perPage must be a positive integer'
            );
        }

        // set page param
        $this->page = $page < 0 ?
            0 :
            $page;

        // set per page param
        $this->perPage = $perPage > self::MAX_ALLOWED_ITEMS_PER_PAGE ?
            self::MAX_ALLOWED_ITEMS_PER_PAGE :
            $perPage;

        $this->perPage = $this->perPage <= 0 ?
            1 :
            $this->perPage;
    }

    /**
     * Return an associative array containing page and perPage attributes
     * according to the Soldo API documentation
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::PAGE_KEY => $this->page,
            self::PER_PAGE_KEY => $this->perPage,
        ];
    }
}
