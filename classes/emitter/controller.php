<?php namespace logstore_xapi\emitter;

class controller {
    protected $repo;
    public static $routes = [
        'course_viewed' => 'course_viewed',
        'discussion_viewed' => 'discussion_viewed',
        'module_viewed' => 'module_viewed',
        'attempt_started' => 'attempt_started',
        'attempt_abandoned' => 'attempt_completed',
        'attempt_completed' => 'attempt_completed',
        'attempt_question_completed' => 'question_answered',
        'user_loggedin' => 'user_logged_in',
        'user_loggedout' => 'user_logged_out',
        'assignment_graded' => 'assignment_graded',
        'assignment_submitted' => 'assignment_submitted',
        'user_registered' => 'user_registered',
        'enrolment_created' => 'enrolment_created',
        'scorm_launched' => 'scorm_launched',
    ];

    /**
     * Constructs a new controller.
     * @param Repository $repo
     */
    public function __construct(repository $repo) {
        $this->repo = $repo;
    }

    /**
     * Creates a new event.
     * @param [String => Mixed] $events
     * @return [String => Mixed]
     */
    public function create_events(array $events) {
        $statements = [];
        foreach ($events as $index => $opts) {
            $route = isset($opts['recipe']) ? $opts['recipe'] : '';
            if (isset(static::$routes[$route])) {
                $event = '\\logstore_xapi\\emitter\\events\\'.static::$routes[$route];
                $service = new $event($this->repo);
                $opts['context_lang'] = $opts['context_lang'] ?: 'en';
                array_push($statements, $service->read($opts));
            }
        }

        return $this->repo->create_events($statements);
    }
}
