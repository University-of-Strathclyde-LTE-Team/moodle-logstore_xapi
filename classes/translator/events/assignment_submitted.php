<?php namespace logstore_xapi\translator\events;

class assignment_submitted extends module_viewed {
    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override module_viewed
     */
    public function read(array $opts) {
        return [array_merge(parent::read($opts)[0], [
            'recipe' => 'assignment_submitted',
        ])];
    }
}