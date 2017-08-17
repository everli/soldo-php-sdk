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
     * @var int
     */
    protected $pages;

    /**
     * @var int
     */
    protected $total;

    /**
     * @var int
     */
    protected $pageSize;

    /**
     * @var int
     */
    protected $currentPage;

    /**
     * @var int
     */
    protected $resultsSize;

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $itemType;


    /**
     * Collection constructor.
     * @param $itemType
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
     * @param $data
     * @return $this
     * @throws SoldoInvalidCollectionException
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

        if(!$this->validateRawData($data, $rules)) {
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
     * @param $items
     */
    private function build($items)
    {
        foreach ($items as $item) {
            $this->items[] = new $this->itemType($item);
        }
    }
}
