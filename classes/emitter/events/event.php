<?php namespace logstore_xapi\emitter\events;

use \logstore_xapi\emitter\repository;

abstract class event {
    protected static $verb_display;
    protected $repo;

    /**
     * Constructs a new event.
     * @param repository $repo
     */
    public function __construct(repository $repo) {
        $this->repo = $repo;
    }

    /**
     * Creates an event in the repository.
     * @param [string => mixed] $event
     * @return [string => mixed]
     */
    public function create(array $event) {
        return $this->repo->create_event($event);
    }

    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     */
    public function read(array $opts) {

        return [
            'actor' => $this->read_user($opts, 'user'),
            'context' => [
                'platform' => $opts['context_platform'],
                'language' => $opts['context_lang'],
                'extensions' => [
                    $opts['context_ext_key'] => $opts['context_ext'],
                    'http://lrs.learninglocker.net/define/extensions/info' => $opts['context_info'],
                ],
                'contextActivities' => [
                    'grouping' => [
                        $this->read_app($opts)
                    ],
                    'category' => [
                        $this->read_source($opts)
                    ]
                ],
            ],
            'timestamp' => $opts['time'],
        ];

    }

    protected function read_user(array $opts, $key) {
        return [
            'name' => $opts[$key.'_name'],
            'account' => [
                'homePage' => $opts[$key.'_url'],
                'name' => $opts[$key.'_id'],
            ],
        ];
    }

    protected function read_activity(array $opts, $key) {
        $activity = [
            'id' => $opts[$key.'_url'],
            'definition' => [
                'type' => $opts[$key.'_type'],
                'name' => [
                    $opts['context_lang'] => $opts[$key.'_name'],
                ],
                'description' => [
                    $opts['context_lang'] => $opts[$key.'_description'],
                ],
            ],
        ];

        if (isset($opts[$key.'_ext']) && isset($opts[$key.'_ext_key'])) {
            $activity['definition']['extensions'] = [];
            $activity['definition']['extensions'][$opts[$key.'_ext_key']] = $opts[$key.'_ext'];
        }

        return $activity;
    }

    protected function read_course($opts) {
        return $this->read_activity($opts, 'course');
    }

    protected function read_app($opts) {
        return $this->read_activity($opts, 'app');
    }

    protected function read_source($opts) {
        return $this->read_activity($opts, 'source');
    }

    protected function read_module($opts) {
        return $this->read_activity($opts, 'module');
    }

    protected function read_discussion($opts) {
        return $this->read_activity($opts, 'discussion');
    }

    protected function read_question($opts) {
        $opts['question_type'] = 'http://adlnet.gov/expapi/activities/cmi.interaction';
        $question = $this->read_activity($opts, 'question');

        $question['definition']['interactionType'] = $opts['interaction_type'];
        $question['definition']['correctResponsesPattern'] = $opts['interaction_correct_responses'];

        $supportedComponentLists = [
            'choice' => ['choices'],
            'sequencing' => ['choices'],
            'likert' => ['scale'],
            'matching' => ['source', 'target'],
            'performance' => ['steps'],
            'true-false' => [],
            'fill-in' => [],
            'long-fill-in' => [],
            'numeric' => [],
            'other' => []
        ];

        foreach ($supportedComponentLists[$opts['interaction_type']] as $index => $listtype) {
            if (isset($opts['interaction_'.$listtype]) && !is_null($opts['interaction_'.$listtype])) {
                $componentlist = [];
                foreach ($opts['interaction_'.$listtype] as $id => $description) {
                    array_push($componentlist, (object)[
                        'id' => (string) $id,
                        'description' => [
                            $opts['context_lang'] => $description,
                        ]
                    ]);
                }
                $question['definition'][$listtype] = $componentlist;
            }
        }
        return $question;
    }

    protected function read_verb_display($opts) {
        $lang = $opts['context_lang'];
        $lang = isset(static::$verb_display[$lang]) ? $lang : array_keys(static::$verb_display)[0];
        return [$lang => static::$verb_display[$lang]];
    }
}
