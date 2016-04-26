<?php namespace logstore_xapi\expander\events;

use logstore_xapi\expander\repository;

class event {

    protected $repo;

    /**
     * Constructs a new event.
     * @param repository $repo
     */
    public function __construct(repository $repo) {
        $this->repo = $repo;
    }

    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     */
    public function read(array $opts) {
        return [
            'user' => $opts['userid'] < 1 ? null : $this->repo->read_user($opts['userid']),
            'relateduser' => $opts['relateduserid'] < 1 ? null : $this->repo->read_user($opts['relateduserid']),
            'course' => $this->repo->read_course($opts['courseid']),
            'app' => $this->repo->read_site(),
            'info' => (object) [
                'https://moodle.org/' => $this->repo->read_release(),
            ],
            'event' => $opts,
        ];
    }
}
