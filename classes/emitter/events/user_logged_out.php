<?php namespace logstore_xapi\emitter\events;

class user_logged_out extends event {
    protected static $verb_display = [
        'en' => 'logged out of'
    ];

    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override event
     */
    public function read(array $opts) {
        return array_merge(parent::read($opts), [
            'verb' => [
                'id' => 'https://brindlewaye.com/xAPITerms/verbs/loggedout/',
                'display' => $this->read_verb_display($opts),
            ],
            'object' => $this->read_app($opts),
        ]);
    }
}