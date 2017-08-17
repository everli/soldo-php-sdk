<?php

namespace Soldo\Resources;

use Soldo\Exceptions\SoldoInvalidCollectionException;
use Soldo\Exceptions\SoldoInvalidResourceException;
use Soldo\Validators\ResourceValidatorTrait;
use Soldo\Validators\ValidatorTrait;

/**
 * Class Collection
 * @package Soldo\Resources
 */
class Collection
{
    use ValidatorTrait, ResourceValidatorTrait;

    /**
     * Number of available pages
     *
     * @var int
     */
    protected $pages;

    /**
     * Number of total available items
     *
     * @var int
     */
    protected $total;

    /**
     * The number of elements per page
     *
     * @var int
     */
    protected $pageSize;

    /**
     * The current page 0 based
     *
     * @var int
     */
    protected $currentPage;

    /**
     * The number of the returned items
     *
     * @var int
     */
    protected $resultsSize;

    /**
     * Array containing all collection items
     *
     * @var array
     */
    protected $items = [];

    /**
     * Collection remote path
     *
     * @var string
     */
    protected $path;

    /**
     * A valid Soldo resource class name (e.g Soldo\Resources\Card)
     *
     * @var string
     */
    protected $itemType;

    /**
     * Collection constructor.
     * @param string $itemType
     * @throws SoldoInvalidResourceException
     */
    public function __construct($itemType)
    {
        $this->validateClassName($itemType);

        $this->itemType = $itemType;
        $this->path = call_user_func([$itemType, 'getBasePath']);
    }

    /**
     * Validate rawData, build the resources and populate the collection
     *
     * @param array $data
     * @throws SoldoInvalidCollectionException
     * @return $this
     */
    public function fill($data)
    {
        $rules = [
            'pages' => 'integer',
            'total' => 'integer',
            'page_size' => 'integer',
            'current_page' => 'integer',
            'results_size' => 'integer',
            'results' => 'array',
        ];

        if (!$this->validateRawData($data, $rules)) {
            throw new SoldoInvalidCollectionException(
                'Could not generate a collection with data provided'
            );
        }

        $this->pages = $data['pages'];
        $this->total = $data['total'];
        $this->pageSize = $data['page_size'];
        $this->currentPage = $data['current_page'];
        $this->resultsSize = $data['results_size'];

        $this->build($data['results']);

        return $this;
    }

    /**
     * Return array of resources
     *
     * @return array
     */
    public function get()
    {
        return $this->items;
    }

    /**
     * Get the collection remote path
     *
     * @return string
     */
    public function getRemotePath()
    {
        return $this->path;
    }

    /**
     * Populate $this->items with the list of resources
     *
     * @param array $items
     */
    private function build($items)
    {
        foreach ($items as $item) {
            $this->items[] = new $this->itemType($item);
        }
    }
}
