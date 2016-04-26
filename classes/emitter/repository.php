<?php namespace logstore_xapi\emitter;
use \TinCan\RemoteLRS as TinCanRemoteLrs;
use \TinCan\Statement as TinCanStatement;

class repository {
    protected $store;

    /**
     * Constructs a new Repository.
     * @param TinCanRemoteLrs $store
     * @param PhpObj $cfg
     */
    public function __construct(TinCanRemoteLrs $store) {
        $this->store = $store;
    }

    /**
     * Creates an event in the store.
     * @param [string => mixed] $statements
     * @return [string => mixed]
     */
    public function create_events(array $statements) {
        $this->store->saveStatements($statements);
        return $statements;
    }
}
