<?php namespace logstore_xapi\emitter\events;

class enrolment_created extends event {
    protected static $verb_display = [
        'en' => 'enrolled onto'
    ];

    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override event
     */
    public function read(array $opts) {
        return array_merge_recursive(parent::read($opts), [
            'verb' => [
                'id' => 'http://www.tincanapi.co.uk/verbs/enrolled_onto_learning_plan',
                'display' => $this->read_verb_display($opts),
            ],
            'object' => $this->read_course($opts),
            'context' => [
                'instructor' => $this->read_user($opts, 'instructor'),
            ],
        ]);
    }
}