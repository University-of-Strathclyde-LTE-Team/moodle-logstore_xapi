<?php namespace logstore_xapi\translator\events;

class user_logged_out extends user_logged_in {
    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override user_logged_in
     */
    public function read(array $opts) {
        return [array_merge(parent::read($opts)[0], [
            'recipe' => 'user_loggedout',
        ])];
    }
}