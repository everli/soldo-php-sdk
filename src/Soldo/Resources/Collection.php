<?php

namespace Soldo\Resources;

use Respect\Validation\Validator;
use Soldo\Exceptions\SoldoInvalidCollectionException;
use Soldo\Exceptions\SoldoInvalidPathException;

/**
 * Class Collection
 * @package Soldo\Resources
 */
class Collection
{
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
     */
    public function __construct($itemType)
    {
        $this->validateItemType($itemType);
        $this->itemType = $itemType;
        $this->path =  call_user_func([$itemType, 'getBasePath']);
    }

    /**
     * Fill collection starting from raw data
     *
     * @param $data
     * @return $this
     */
    public function fill($data)
    {
        $this->validateRawData($data);

        $this->pages = $data['pages'];
        $this->total = $data['total'];
        $this->pageSize = $data['page_size'];
        $this->currentPage = $data['current_page'];
        $this->resultsSize = $data['results_size'];

        $this->build($data['results']);

        return $this;
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getRemotePath()
    {
        return $this->path;
    }

    /**
     * @param $items
     */
    private function build($items)
    {
        $item_class_name = $this->itemType;
        foreach ($items as $item) {
            $this->items[] = new $item_class_name($item);
        }
    }

    /**
     * @param $itemType
     * @return bool
     */
    private function validateItemType($itemType)
    {
        if ($itemType === null) {
            throw new \InvalidArgumentException(
                'Could not generate a Soldo collection. '
                . '$itemType must be a valid Resource child class name'
            );
        }

        if (class_exists($itemType) === false) {
            throw new \InvalidArgumentException(
                'Could not generate a Soldo collection '
                . $itemType . ' doesn\'t exist'
            );
        }

        // create a dummy object and check if it is a Resource child
        $dummy = new $itemType();
        if (is_a($dummy, '\Soldo\Resources\Resource') === false) {
            throw new \InvalidArgumentException(
                'Could not generate a Soldo collection '
                . $itemType . ' is not a Resource child'
            );
        }

        return true;
    }

    /**
     * @param $data
     * @throws SoldoInvalidCollectionException
     * @return bool
     */
    private function validateRawData($data)
    {
        $validator = Validator::key('pages', Validator::intVal())
            ->key('total', Validator::intVal())
            ->key('page_size', Validator::intVal())
            ->key('current_page', Validator::intVal())
            ->key('results_size', Validator::intVal())
            ->key('results', Validator::arrayType());

        if ($validator->validate($data) === false) {
            throw new SoldoInvalidCollectionException(
                'Could not generate a Soldo collection '
                . 'with the array provided'
            );
        }

        return true;
    }
}
