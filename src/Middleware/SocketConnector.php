<?php

namespace PhilKra\Middleware;

use PhilKra\Stores\ErrorsStore;
use PhilKra\Stores\TransactionsStore;
use PhilKra\Serializers\Errors;
use PhilKra\Serializers\Transactions;


/**
 *
 * Middleware which Transmits the Data to the Endpoints via a file socket
 *
 */
class SocketConnector implements V1ApiConnectorInterface
{
    /**
     * Agent Config
     *
     * @var \PhilKra\Helper\Config
     */
    private $config;

    
    /**
     * @param \PhilKra\Helper\Config $config
     */
    public function __construct(\PhilKra\Helper\Config $config)
    {
        $this->config = $config;

    }

    /**
     * Push the Transactions to APM Server via 'v1_trans_sock'
     *
     * @param \PhilKra\Stores\TransactionsStore $store
     *
     * @return bool
     */
    public function sendTransactions(TransactionsStore $store) : bool
    {
        $payload = json_encode(new Transactions($this->config, $store));
        $socketFile = $this->config->get('v1_trans_sock');
        
        return $this->writeToSocket($socketFile, $payload);
    }

    /**
     * Push the Errors to APM Server via 'v1_err_sock'
     *
     * @param \PhilKra\Stores\ErrorsStore $store
     *
     * @return bool
     */
    public function sendErrors(ErrorsStore $store) : bool
    {
        $payload = json_encode(new Errors($this->config, $store));
        $socketFile = $this->config->get('v1_err_sock');
        
        return $this->writeToSocket($socketFile, $payload);
    }

    protected function writeToSocket($socketFile, $payload)
    {
        $timeout = $this->config->get('socket_timeout');
        $socket = @stream_socket_client($socketFile, $errorno, $errorstr, $timeout);
        if ($socket == false) {
            return false;//throw new \RuntimeException($socketFile . ' failed: ' . $errorstr);
        }
        @stream_set_timeout($socket, $timeout);
        $written = @fwrite($socket, $payload."\n");

        if ($written == false){
            return false;//throw new \RuntimeException('Writting to socket failed: ' . $socketFile);
        }
        return true;
        
    }
}
