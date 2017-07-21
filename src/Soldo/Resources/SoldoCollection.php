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
     *
     */
    protected $items;

    /**
     * SoldoCollection constructor.
     * @param $data
     * @param $className
     */
    public function __construct($data, $className)
    {
        $this->validateRawData($data);
        $this->validateClassName($className);


        $this->pages = $data['pages'];
        $this->total = $data['total'];
        $this->pageSize = $data['page_size'];
        $this->currentPage = $data['current_page'];
        $this->resultsSize = $data['results_size'];
        $this->items = $this->build($data['results'], $className);

    }


    /**
     * @return array
     */
    public function get()
    {
        return $this->items;
    }

    /**
     * @param $items
     * @param $className
     * @return array
     */
    private function build($items, $className)
    {
        $resources = [];

        foreach ($items as $item) {
            /** @var SoldoResource $resource */
            $resource = new $className();
            $resource->update($item);
            $resources[] = $resource;
        }

        return $resources;
    }


    /**
     * @param $className
     */
    private function validateClassName($className)
    {
        if(class_exists($className) === false) {
            throw new InvalidArgumentException(
                'Could not generate a Soldo collection '
                .$className . 'doesn\'t exists'
            );
        }
    }


    /**
     * @param $data
     * @return bool
     * @throws SoldoInvalidCollectionException
     */
    private function validateRawData($data)
    {
        $validator = Validator::key('pages', Validator::intVal())
            ->key('total', Validator::intVal())
            ->key('page_size', Validator::intVal())
            ->key('current_page', Validator::intVal())
            ->key('results_size', Validator::intVal())
            ->key('results', Validator::arrayType());

        if($validator->validate($data) === false) {
            throw new SoldoInvalidCollectionException(
                'Could not generate a Soldo collection '
                .'with the array provided'
            );
        }

        return true;
    }
}
