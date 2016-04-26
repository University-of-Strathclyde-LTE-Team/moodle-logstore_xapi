<?php namespace logstore_xapi\emitter\events;

abstract class viewed extends event {
    protected static $verb_display = [
        'en' => 'viewed'
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
                'id' => 'http://id.tincanapi.com/verb/viewed',
                'display' => $this->read_verb_display($opts),
            ],
        ]);
    }
}