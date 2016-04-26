<?php namespace logstore_xapi\expander\events;

class discussion_event extends event {
    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override event
     */
    public function read(array $opts) {
        $discussion = $this->repo->read_discussion($opts['objectid']);
        return array_merge(parent::read($opts), [
            'discussion' => $discussion,
            'module' => $this->repo->read_module($discussion->forum, 'forum'),
        ]);
    }
}