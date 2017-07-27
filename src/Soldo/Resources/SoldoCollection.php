<?php

namespace Soldo\Resources;

use Psr\Log\InvalidArgumentException;
use Respect\Validation\Validator;
use Soldo\Exceptions\SoldoInvalidCollectionException;

class SoldoCollection
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
    protected $itemType = SoldoResource::class;

    /**
     * SoldoCollection constructor.
     * @param $data
     * @param $className
     */
    public function __construct()
    {
        $this->validateItemType();
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
     * @param $className
     */
    private function build($items)
    {
        $item_class_name = $this->itemType;
        foreach ($items as $item) {
            $this->items[] = new $item_class_name($item);
        }
    }

    /**
     * @param $className
     * @return bool
     */
    private function validateItemType()
    {
        if (class_exists($this->itemType) === false) {
            throw new InvalidArgumentException(
                'Could not generate a Soldo collection '
                . $this->itemType . 'doesn\'t exist'
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
