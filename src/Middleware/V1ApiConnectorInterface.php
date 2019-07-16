<?php
namespace PhilKra\Middleware;

use PhilKra\Stores\ErrorsStore;
use PhilKra\Stores\TransactionsStore;


/**
 *
 * Middleware which Transmits the Data to the V1 API Endpoints
 *
 */
interface V1ApiConnectorInterface
{
    /**
     * Push the Transactions to APM Server V1 API
     *
     * @param \PhilKra\Stores\TransactionsStore $store
     *
     * @return bool
     */
    public function sendTransactions(TransactionsStore $store) : bool;

    /**
     * Push the Errors to APM Server V1 API
     *
     * @param \PhilKra\Stores\ErrorsStore $store
     *
     * @return bool
     */
    public function sendErrors(ErrorsStore $store) : bool;
}