<?php namespace logstore_xapi\emitter\events;

class attempt_started extends event {
    protected static $verb_display = [
        'en' => 'started'
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
                'id' => 'http://activitystrea.ms/schema/1.0/start',
                'display' => $this->read_verb_display($opts),
            ],
            'object' => [
                'id' => $opts['attempt_url'],
                'definition' => [
                    'type' => $opts['attempt_type'],
                    'name' => [
                        $opts['context_lang'] => $opts['attempt_name'],
                    ],
                    'extensions' => [
                        $opts['attempt_ext_key'] => $opts['attempt_ext']
                    ],
                ],
            ],
            'context' => [
                'contextActivities' => [
                    'grouping' => [
                        $this->read_course($opts),
                        $this->read_module($opts),
                    ],
                ],
            ],
        ]);
    }
}