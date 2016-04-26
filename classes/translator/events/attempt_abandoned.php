<?php namespace logstore_xapi\translator\events;

class attempt_abandoned extends attempt_reviewed {
    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override attempt_reviewed
     */
    public function read(array $opts) {
        return [array_merge(parent::read($opts)[0], [
            'recipe' => 'attempt_abandoned'
        ])];
    }
}