<?php namespace logstore_xapi\emitter\events;

class question_answered extends event {
    protected static $verb_display = [
        'en' => 'answered'
    ];

    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override event
     */
    public function read(array $opts) {

        $statement = [
            'verb' => [
                'id' => 'http://adlnet.gov/expapi/verbs/answered',
                'display' => $this->read_verb_display($opts),
            ],
            'result' => [
                'score' => [
                    'raw' => $opts['attempt_score_raw'],
                    'min' => $opts['attempt_score_min'],
                    'max' => $opts['attempt_score_max'],
                    'scaled' => $opts['attempt_score_scaled']
                ],
                'completion' => $opts['attempt_completed'],
                'response' => $opts['attempt_response']
            ],
            'object' => $this->read_question($opts),
            'context' => [
                'contextActivities' => [
                    'parent' => [
                        $this->read_module($opts)
                    ],
                    'grouping' => [
                        $this->read_course($opts),
                        [
                            'id' => $opts['attempt_url']
                        ],
                    ],
                ],
            ],
        ];

        if (!is_null($opts['attempt_success'])) {
            $statement['result']['success'] = $opts['attempt_success'];
        }

        return array_merge_recursive(parent::read($opts), $statement);
    }
}