<?php namespace logstore_xapi\expander\events;

class assignment_graded extends event {
    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override event
     */
    public function read(array $opts) {
        $grade = $this->repo->read_object($opts['objectid'], $opts['objecttable']);
        $grade_comment = $this->repo->read_grade_comment($grade->id, $grade->assignment)->commenttext;
        $grade_items = $this->repo->read_grade_items($grade->assignment, 'assign');
        return array_merge(parent::read($opts), [
            'grade' => $grade,
            'grade_comment' => $grade_comment,
            'grade_items' => $grade_items,
            'graded_user' => $this->repo->read_user($grade->userid),
            'module' => $this->repo->read_module($grade->assignment, 'assign'),
        ]);
    }
}