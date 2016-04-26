<?php namespace logstore_xapi\translator\events;

class module_viewed extends course_viewed {
    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override course_viewed
     */
    public function read(array $opts) {
        return [array_merge(parent::read($opts)[0], [
            'recipe' => 'module_viewed',
            'module_url' => $opts['module']->url,
            'module_name' => $opts['module']->name,
            'module_description' => $opts['module']->intro ?: 'A module',
            'module_type' => static::$xapi_type.$opts['module']->type,
            'module_ext' => $opts['module'],
            'module_ext_key' => 'http://lrs.learninglocker.net/define/extensions/moodle_module'
        ])];
    }
}