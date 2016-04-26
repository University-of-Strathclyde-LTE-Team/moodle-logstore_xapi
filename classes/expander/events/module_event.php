<?php namespace logstore_xapi\expander\events;

class module_event extends event {
    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override event
     */
    public function read(array $opts) {
        return array_merge(parent::read($opts), [
            'module' => $this->repo->read_module($opts['objectid'], $opts['objecttable']),
        ]);
    }
}