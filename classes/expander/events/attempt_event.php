<?php namespace logstore_xapi\expander\events;

class attempt_event extends event {
    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override event
     */
    public function read(array $opts) {
        $attempt = $this->repo->read_attempt($opts['objectid']);
        $grade_items = $this->repo->read_grade_items($attempt->quiz, 'quiz');
        $attempt->questions = $this->repo->read_questionAttempts($attempt->id);
        $questions = $this->repo->read_questions($attempt->quiz);

        return array_merge(parent::read($opts), [
            'attempt' => $attempt,
            'module' => $this->repo->read_module($attempt->quiz, 'quiz'),
            'grade_items' => $grade_items,
            'questions' => $questions
        ]);
    }
}