<?php namespace logstore_xapi\emitter\events;

class assignment_graded extends event {
    protected static $verb_display = [
        'en' => 'recieved grade for'
    ];

    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override event
     */
    public function read(array $opts) {
        $instructor = parent::read($opts)['actor'];
        $statement =  array_merge_recursive(parent::read($opts), [
            'verb' => [
                'id' => 'http://adlnet.gov/expapi/verbs/scored',
                'display' => $this->read_verb_display($opts),
            ],
            'result' => [
                'score' => [
                    'raw' => $opts['grade_score_raw'],
                    'min' => $opts['grade_score_min'],
                    'max' => $opts['grade_score_max'],
                    'scaled' => $opts['grade_score_scaled']
                ],
                'completion' => $opts['grade_completed'],
                'response' => $opts['grade_comment']
            ],
            'object' => $this->read_module($opts),
            'context' => [
                'contextActivities' => [
                    'parent' => [
                        $this->read_course($opts),
                    ],
                ],
                'instructor' => $instructor
            ],
        ]);

        //Excluded from array merge to make sure that the actor is overwritten e.g. if a different IFI is used. 
        $statement['actor'] = [
            'objectType' => 'Agent',
            'name' => $opts['graded_user_name'],
            'account' => [
                'homePage' => $opts['graded_user_url'],
                'name' => $opts['graded_user_id'],
            ],
        ];

        if (!is_null($opts['grade_success'])) {
            $statement['result']['success'] = $opts['grade_success'];
        }

        return $statement;
    }
}