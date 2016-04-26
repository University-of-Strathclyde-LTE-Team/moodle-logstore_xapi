<?php namespace logstore_xapi\emitter\events;

class scorm_launched extends event {
    protected static $verb_display = [
        'en' => 'launched'
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
                'id' => 'http://adlnet.gov/expapi/verbs/launched',
                'display' => $this->read_verb_display($opts),
            ],
            'object' => $this->read_module($opts),
            'context' => [
                'contextActivities' => [
                    'grouping' => [
                        $this->read_course($opts),
                    ],
                ],
            ],
        ]);
    }
}