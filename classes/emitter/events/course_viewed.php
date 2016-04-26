<?php namespace logstore_xapi\emitter\events;

class course_viewed extends viewed {
    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override event
     */
    public function read(array $opts) {
        return array_merge(parent::read($opts), [
            'object' => $this->read_course($opts),
        ]);
    }
}