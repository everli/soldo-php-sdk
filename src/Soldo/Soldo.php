<?php
/**
 * Copyright 2017 Supermercato24.
 *
 * Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur blandit tempus porttitor. Maecenas sed diam
 * eget risus varius blandit sit amet non magna. Donec sed odio dui. Vivamus sagittis lacus vel augue laoreet rutrum
 * faucibus dolor auctor.
 *
 */
namespace Soldo;

use Soldo\Exceptions\SoldoSDKException;

/**
 * Class Soldo
 *
 * @package Soldo
 */
class Soldo
{

    /**
     * Soldo constructor
     *
     * @param array $config
     * @throws SoldoSDKException
     */
    public function _construct(array $config = [])
    {
        $config = array_merge(
            [
                'mode' => 'live',
                'log.enabled' => false,
                'log.file' => null,
                'log.level' => 'WARNING',
            ],
            $config
        );

        if(!$config['client_id']) {
            throw new SoldoSDKException('Required "client_id" key not supplied in config');
        }

        if(!$config['client_secret']) {
            throw new SoldoSDKException('Required "client_secret" key not supplied in config');
        }

    }
}

