<?php

namespace Soldo;

use Soldo\Exceptions\SoldoInvalidEvent;
use Soldo\Validators\ValidatorTrait;

/**
 * Class SoldoWebhook
 * @package Soldo
 */
class SoldoEvent
{

    use ValidatorTrait;

    /**
     * Define webhook supported data types
     */
    const EVENT_TYPE_CARD = 'Card';
    const EVENT_TYPE_TRANSACTION = 'Transaction';
    const EVENT_TYPE_EMPLOYEE = 'Employee';
    const EVENT_TYPE_EXPENSE_CENTRE = 'ExpenseCentre';

    /**
     * An identifier for the event
     *
     * @var string
     */
    private $type;

    /**
     * The ordered list need to build the resource fingerprint
     *
     * @var array
     */
    private $fingerprintOrder;

    /**
     * Fingerprint contained in the X-Soldo-Fingerprint request header
     *
     * @var boolean
     */
    private $fingerprint;

    /**
     * The resource that triggered the event
     *
     * @var \Soldo\Resources\Resource
     */
    private $resource;


    /**
     * SoldoWebhook constructor.
     * @param array $data
     * @param string $fingerprint
     * @param string $fingerprintOrder
     * @throws SoldoInvalidEvent
     */
    public function __construct($data, $fingerprint, $fingerprintOrder)
    {
        $rules = [
            'event_type' => 'required',
            'event_name' => 'required',
            'data' => 'array',
        ];

        if (!$this->validateRawData($data, $rules)) {
            throw new SoldoInvalidEvent(
                'Invalid webhook data'
            );
        }

        if (!in_array($data['event_type'], self::types())) {
            throw new SoldoInvalidEvent(
                'Event type not supported'
            );
        }

        // build resource
        $className = '\Soldo\Resources\\' . $data['event_type'];
        $this->resource = new $className($data['data']);
        $this->type = $this->resource->getEventType();

        $this->fingerprint = $fingerprint;
        $this->fingerprintOrder = explode(',', $fingerprintOrder);
    }

    /**
     * Return the event type
     *
     * @return null|string
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * Return the resource if the generated fingerprint matches $this->fingerprint
     * Throw an exception otherwise
     *
     * @param string $internalToken
     * @return Resources\Resource
     * @throws SoldoInvalidEvent
     */
    public function get($internalToken)
    {
        $fingerprint = $this->resource->buildFingerprint($this->fingerprintOrder, $internalToken);
        if ($fingerprint !== $this->fingerprint) {
            throw new SoldoInvalidEvent(
                'Cannot verify the given fingerprint'
            );
        }

        return $this->resource;
    }

    /**
     * Get supported data types
     *
     * @return array
     */
    public static function types()
    {
        return [
            self::EVENT_TYPE_CARD,
            self::EVENT_TYPE_TRANSACTION,
            self::EVENT_TYPE_EMPLOYEE,
            self::EVENT_TYPE_EXPENSE_CENTRE,
        ];
    }




}
