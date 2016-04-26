<?php namespace logstore_xapi\emitter\events;

class user_registered extends event {
    protected static $verb_display = [
        'en' => 'registered to'
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
                'id' => 'http://adlnet.gov/expapi/verbs/registered',
                'display' => $this->read_verb_display($opts),
            ],
            'object' => $this->read_app($opts),
        ]);
    }
}