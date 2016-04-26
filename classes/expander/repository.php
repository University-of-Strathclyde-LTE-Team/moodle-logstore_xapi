<?php namespace logstore_xapi\expander;

class repository {
    protected $store;
    protected $cfg;

    /**
     * Constructs a new repository.
     * @param $store
     * @param \stdClass $cfg
     */
    public function __construct($store, $cfg) {
        $this->store = $store;
        $this->cfg = $cfg;
    }

    /**
     * Reads an object from the store with the given type and query.
     * @param String $type
     * @param [String => Mixed] $query
     * @return PhpObj
     */
    protected function read_store_record($type, array $query) {
        $model = $this->store->get_record($type, $query);
        return $model;
    }

    /**
     * Reads an array of objects from the store with the given type and query.
     * @param String $type
     * @param [String => Mixed] $query
     * @return PhpArr
     */
    protected function read_store_records($type, array $query) {
        $model = $this->store->get_records($type, $query);
        return $model;
    }

    /**
     * Reads an object from the store with the given id.
     * @param String $id
     * @param String $type
     * @return PhpObj
     */
    public function read_object($id, $type) {
        $model = $this->read_store_record($type, ['id' => $id]);
        $model->type = $type;
        return $model;
    }

    /**
     * Reads an object from the store with the given id.
     * @param String $id
     * @param String $type
     * @return PhpObj
     */
    public function read_module($id, $type) {
        $model = $this->read_object($id, $type);
        $module = $this->read_store_record('modules', ['name' => $type]);
        $course_module = $this->read_store_record('course_modules', [
            'instance' => $id,
            'module' => $module->id,
            'course' => $model->course
        ]);
        $model->url = $this->cfg->wwwroot . '/mod/'.$type.'/view.php?id=' . $course_module->id;
        return $model;
    }

    /**
     * Reads a quiz attempt from the store with the given id.
     * @param String $id
     * @return PhpObj
     */
    public function read_attempt($id) {
        $model = $this->read_object($id, 'quiz_attempts');
        $model->url = $this->cfg->wwwroot . '/mod/quiz/attempt.php?attempt='.$id;
        $model->name = 'Attempt '.$id;
        return $model;
    }

    /**
     * Reads question attempts from the store with the given quiz attempt id.
     * @param String $id
     * @return PhpArr
     */
    public function read_questionAttempts($id) {
        $questionAttempts = $this->read_store_records('question_attempts', ['questionusageid' => $id]);
        foreach ($questionAttempts as $questionIndex => $questionAttempt) {
            $questionAttemptSteps = $this->read_store_records('question_attempt_steps', ['questionattemptid' => $questionAttempt->id]);
            foreach ($questionAttemptSteps as $stepIndex => $questionAttemptStep) {
                $questionAttemptStep->data = $this->read_store_records('question_attempt_step_data', ['attemptstepid' => $questionAttemptStep->id]);
            }
            $questionAttempt->steps = $questionAttemptSteps;
        }
        return $questionAttempts;
    }

    /**
     * Reads questions from the store with the given quiz id.
     * @param String $id
     * @return PhpArr
     */
    public function read_questions($quizId) {
        $quizSlots = $this->read_store_records('quiz_slots', ['quizid' => $quizId]);
        $questions = [];
        foreach ($quizSlots as $index => $quizSlot) {
            $question = $this->read_store_record('question', ['id' => $quizSlot->questionid]);
            $question->answers = $this->read_store_records('question_answers', ['question' => $question->id]);
            $question->url = $this->cfg->wwwroot . '/mod/question/question.php?id='.$question->id;
            $questions[$question->id] = $question;
        }

        return $questions;
    }

    /**
     * Reads  grade metadata from the store with the given type and id.
     * @param String $id
     * @param String $type
     * @return PhpObj
     */
    public function read_grade_items($id, $type) {
        return $this->read_store_record('grade_items', ['itemmodule' => $type, 'iteminstance' => $id]);
    }

    /**
     * Reads assignemnt grade comment from the store for a given grade and assignment id
     * @param String $id
     * @return PhpObj
     */
    public function read_grade_comment($grade_id, $assignment_id) {
        $model = $this->read_store_record(
            'assignfeedback_comments',
            [
                'assignment' => $assignment_id,
                'grade' => $grade_id
            ]
        );
        return $model;
    }


    /**
     * Reads a course from the store with the given id.
     * @param String $id
     * @return PhpObj
     */
    public function read_course($id) {
        $model = $this->read_object($id, 'course');
        $model->url = $this->cfg->wwwroot.($id > 0 ? '/course/view.php?id=' . $id : '');
        return $model;
    }

    /**
     * Reads a user from the store with the given id.
     * @param String $id
     * @return PhpObj
     */
    public function read_user($id) {
        $model = $this->read_object($id, 'user');
        $model->url = $this->cfg->wwwroot;
        return $model;
    }

    /**
     * Reads a discussion from the store with the given id.
     * @param String $id
     * @return PhpObj
     */
    public function read_discussion($id) {
        $model = $this->read_object($id, 'forum_discussions');
        $model->url = $this->cfg->wwwroot . '/mod/forum/discuss.php?d=' . $id;
        return $model;
    }

    /**
     * Reads the Moodle release number.
     * @return String
     */
    public function read_release() {
        return $this->cfg->release;
    }

    /**
     * Reads the Moodle site
     * @return PhpObj
     */
    public function read_site() {
        $model = $this->read_course(1);
        $model->url = $this->cfg->wwwroot;
        $model->type = "site";
        return $model;
    }
}
