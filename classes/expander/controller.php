<?php namespace logstore_xapi\expander;

class controller {

	protected $repo;

	public static $routes = [
			'\core\event\course_viewed' => 'event',
			'\mod_page\event\course_module_viewed' => 'module_event',
			'\mod_quiz\event\course_module_viewed' => 'module_event',
			'\mod_url\event\course_module_viewed' => 'module_event',
			'\mod_folder\event\course_module_viewed' => 'module_event',
			'\mod_forum\event\course_module_viewed' => 'module_event',
			'\mod_forum\event\discussion_viewed' => 'discussion_event',
			'\mod_forum\event\user_report_viewed' => 'module_event',
			'\mod_book\event\course_module_viewed' => 'module_event',
			'\mod_scorm\event\course_module_viewed' => 'module_event',
			'\mod_resource\event\course_module_viewed' => 'module_event',
			'\mod_choice\event\course_module_viewed' => 'module_event',
			'\mod_data\event\course_module_viewed' => 'module_event',
			'\mod_feedback\event\course_module_viewed' => 'module_event',
			'\mod_lesson\event\course_module_viewed' => 'module_event',
			'\mod_lti\event\course_module_viewed' => 'module_event',
			'\mod_wiki\event\course_module_viewed' => 'module_event',
			'\mod_workshop\event\course_module_viewed' => 'module_event',
			'\mod_chat\event\course_module_viewed' => 'module_event',
			'\mod_glossary\event\course_module_viewed' => 'module_event',
			'\mod_imscp\event\course_module_viewed' => 'module_event',
			'\mod_survey\event\course_module_viewed' => 'module_event',
			'\mod_url\event\course_module_viewed' => 'module_event',
			'\mod_facetoface\event\course_module_viewed' => 'module_event',
			'\mod_quiz\event\attempt_abandoned' => 'attempt_event',
			'\mod_quiz\event\attempt_preview_started' => 'attempt_event',
			'\mod_quiz\event\attempt_reviewed' => 'attempt_event',
			'\mod_quiz\event\attempt_viewed' => 'attempt_event',
			'\core\event\user_loggedin' => 'event',
			'\core\event\user_loggedout' => 'event',
			'\mod_assign\event\submission_graded' => 'assignment_graded',
			'\mod_assign\event\assessable_submitted' => 'assignment_submitted',
			'\core\event\user_created' => 'event',
			'\core\event\user_enrolment_created' => 'event',
			'\mod_scorm\event\sco_launched' => 'scorm_launched'
	];

	/**
	 * Constructs a new Controller.
	 *
	 * @param Repository $repo
	 */
	public function __construct(repository $repo) {
		$this->repo = $repo;
	}

	/**
	 * Creates a new event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 */
	public function create_event(array $opts) {
		$route = isset ( $opts ['eventname'] ) ? $opts ['eventname'] : '';
		if (isset ( static::$routes [$route] ) && ($opts ['userid'] > 0 || $opts ['relateduserid'] > 0)) {
			$event = '\logstore_xapi\expander\events\\' . static::$routes [$route];
			return (new $event ( $this->repo ))->read ( $opts );
		} else {
			return null;
		}
	}

	/**
	 * Creates a new event.
	 *
	 * @param
	 *        	[String => Mixed] $events
	 * @return [String => Mixed]
	 */
	public function create_events(array $events) {
		$results = [ ];
		foreach ( $events as $index => $opts ) {
			$route = isset ( $opts ['eventname'] ) ? $opts ['eventname'] : '';
			if (isset ( static::$routes [$route] ) && ($opts ['userid'] > 0 || $opts ['relateduserid'] > 0)) {
				$event = '\\logstore_xapi\\expander\\events\\' . static::$routes [$route];
				array_push ( $results, (new $event ( $this->repo ))->read ( $opts ) );
			}
		}
		return $results;
	}
}
