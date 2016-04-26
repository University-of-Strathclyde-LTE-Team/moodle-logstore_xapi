<?php namespace logstore_xapi\translator\events;

class attempt_started extends module_viewed {
    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override module_viewed
     */
    public function read(array $opts) {
        return [array_merge(parent::read($opts)[0], [
            'recipe' => 'attempt_started',
            'attempt_url' => $opts['attempt']->url,
            'attempt_type' => static::$xapi_type.$opts['attempt']->type,
            'attempt_ext' => $opts['attempt'],
            'attempt_ext_key' => 'http://lrs.learninglocker.net/define/extensions/moodle_attempt',
            'attempt_name' => $opts['attempt']->name,
        ])];
    }
}