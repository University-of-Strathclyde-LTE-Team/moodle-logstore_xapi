<?php namespace logstore_xapi\emitter\events;

class discussion_viewed extends viewed {
    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override event
     */
    public function read(array $opts) {
        return array_merge_recursive(parent::read($opts), [
            'object' => $this->read_discussion($opts, 'discussion', 'http://id.tincanapi.com/activitytype/discussion'),
            'context' => [
                'contextActivities' => [
                    'grouping' => [
                        $this->read_course($opts, 'course', 'http://adlnet.gov/expapi/activities/course'),
                        $this->read_module($opts),
                    ],
                ],
            ],
        ]);
    }
}