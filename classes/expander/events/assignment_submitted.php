<?php namespace logstore_xapi\expander\events;

class assignment_submitted extends event {
    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override event
     */
    public function read(array $opts) {
        $submission = $this->repo->read_object($opts['objectid'], $opts['objecttable']);
        return array_merge(parent::read($opts), [
            'submission' => $submission,
            'module' => $this->repo->read_module($submission->assignment, 'assign'),
        ]);
    }
}