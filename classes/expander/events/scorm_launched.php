<?php namespace logstore_xapi\expander\events;

class scorm_launched extends event {
    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override event
     */
    public function read(array $opts) {
        $scorm_scoes = $this->repo->read_object($opts['objectid'], $opts['objecttable']);
        return array_merge(parent::read($opts), [
            'module' => $this->repo->read_module($scorm_scoes->scorm, 'scorm'),
            'scorm_scoes' => $scorm_scoes
        ]);
    }
}