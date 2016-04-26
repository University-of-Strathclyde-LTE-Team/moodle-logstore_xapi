<?php namespace logstore_xapi\translator;

class controller {

    protected $repo;

    public static $routes = [
        '\core\event\course_viewed' => 'course_viewed',
        '\mod_page\event\course_module_viewed' => 'module_viewed',
        '\mod_quiz\event\course_module_viewed' => 'module_viewed',
        '\mod_url\event\course_module_viewed' => 'module_viewed',
        '\mod_folder\event\course_module_viewed' => 'module_viewed',
        '\mod_forum\event\course_module_viewed' => 'module_viewed',
        '\mod_forum\event\discussion_viewed' =>  'discussion_viewed',
        '\mod_forum\event\user_report_viewed' =>  'module_viewed',
        '\mod_book\event\course_module_viewed' => 'module_viewed',
        '\mod_scorm\event\course_module_viewed' => 'module_viewed',
        '\mod_resource\event\course_module_viewed' => 'module_viewed',
        '\mod_choice\event\course_module_viewed' => 'module_viewed',
        '\mod_data\event\course_module_viewed' => 'module_viewed',
        '\mod_feedback\event\course_module_viewed' => 'module_viewed',
        '\mod_lesson\event\course_module_viewed' => 'module_viewed',
        '\mod_lti\event\course_module_viewed' => 'module_viewed',
        '\mod_wiki\event\course_module_viewed' => 'module_viewed',
        '\mod_workshop\event\course_module_viewed' => 'module_viewed',
        '\mod_chat\event\course_module_viewed' => 'module_viewed',
        '\mod_glossary\event\course_module_viewed' => 'module_viewed',
        '\mod_imscp\event\course_module_viewed' => 'module_viewed',
        '\mod_survey\event\course_module_viewed' => 'module_viewed',
        '\mod_url\event\course_module_viewed' => 'module_viewed',
        '\mod_facetoface\event\course_module_viewed' => 'module_viewed',
        '\mod_quiz\event\attempt_abandoned' => 'attempt_abandoned',
        '\mod_quiz\event\attempt_preview_started' => 'attempt_started',
        '\mod_quiz\event\attempt_reviewed' => ['attempt_reviewed', 'question_submitted'],
        '\mod_quiz\event\attempt_viewed' => 'module_viewed',
        '\core\event\user_loggedin' => 'user_logged_in',
        '\core\event\user_loggedout' => 'user_logged_out',
        '\mod_assign\event\submission_graded' => 'assignment_graded',
        '\mod_assign\event\assessable_submitted' => 'assignment_submitted',
        '\core\event\user_created' => 'user_registered',
        '\core\event\user_enrolment_created' => 'enrolment_created',
        '\mod_scorm\event\sco_launched' => 'scorm_launched',
    ];

    /**
     * Constructs a new controller.
     */
    public function __construct() {}

    /**
     * Creates a new event.
     * @param [String => Mixed] $events
     * @return [String => Mixed]
     */
    public function create_events(array $events) {
        $results = [];
        foreach ($events as $index => $opts) {
            $route = isset($opts['event']['eventname']) ? $opts['event']['eventname'] : '';
            if (isset(static::$routes[$route])) {
                    $route_events = is_array(static::$routes[$route]) ? static::$routes[$route] : [static::$routes[$route]];
                    foreach ($route_events as $route_event) {
                    try {
                        $event = '\\logstore_xapi\\translator\\events\\'.$route_event;
                        foreach ((new $event())->read($opts) as $index => $result) {
                             array_push($results, $result);
                         }
                    } catch (unnecessary_event $ex) {}
                }
            }
        }
        return $results;
    }
}
