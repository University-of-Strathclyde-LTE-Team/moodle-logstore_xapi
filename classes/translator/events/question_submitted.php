<?php namespace logstore_xapi\translator\events;

class question_submitted extends attempt_started {
    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override AttemtStarted
     */
    public function read(array $opts) {
        $translatorevents = [];

        // Push question statements to $translatorevents['events'].
        foreach ($opts['attempt']->questions as $questionId => $question_attempt) {
            array_push(
                $translatorevents,
                $this->question_statement(
                    parent::read($opts)[0],
                    $question_attempt,
                    $opts['questions'][$question_attempt->questionid]
                )
            );
        }

        return $translatorevents;
    }

    /**
     * Build a translator event for an individual question attempt.
     * @param [String => Mixed] $template
     * @param PHPObj $question_attempt
     * @param PHPObj $question
     * @return [String => Mixed]
     */
    protected function question_statement($template, $question_attempt, $question) {

        // For questions, only include data relevant to the current question in the attempt extension.
        $template['attempt_ext']->questions = [$question_attempt];

        $translatorevent = [
            'recipe' => 'attempt_question_completed',
            'attempt_ext' => $template['attempt_ext'],
            'question_attempt_ext' => $question_attempt,
            'question_attempt_ext_key' => 'http://lrs.learninglocker.net/define/extensions/moodle_question_attempt',
            'question_ext' => $question,
            'question_ext_key' => 'http://lrs.learninglocker.net/define/extensions/moodle_question',
            'question_name' => $question->name ?: 'A Moodle quiz question',
            'question_description' => strip_tags($question->questiontext) ?: 'A Moodle quiz question',
            'question_url' => $question->url,
            'attempt_score_scaled' => 0, //default
            'attempt_score_raw' => 0, //default
            'attempt_score_min' => 0, //always 0
            'attempt_score_max' => $question_attempt->maxmark,
            'attempt_response' => $question_attempt->responsesummary, //default
        ];

        $submittedstate = $this->get_last_state($question_attempt);

        if (!is_null($submittedstate->timestamp)) {
            $translatorevent['time'] = date('c', $submittedstate->timestamp);
        }

        $translatorevent = $this->result_from_state($translatorevent, $question_attempt, $submittedstate);

        // Where possible, determine xAPI question type based on available data rather than $question->qtype.
        if (!is_null($question->answers) && ($question->answers !== [])) {
            $translatorevent = $this->multichoice_statement($translatorevent, $question_attempt, $question);
        } else {

            // Other question type.
            $translatorevent['interaction_type'] = "other";
        }

        return array_merge($template, $translatorevent);
    }

    /**
     * Add some result data to translator event for an individual question attempt based on Moodle's question attempt state
     * @param [String => Mixed] $translatorevent
     * @param PHPObj $question_attempt
     * @param PHPObj $submittedstate
     * @return [String => Mixed]
     */
    public function result_from_state($translatorevent, $question_attempt, $submittedstate) {
        switch ($submittedstate->state) {
            case "todo":
                $translatorevent['attempt_completed'] = false;
                $translatorevent['attempt_success'] = null;
                break;
            case "gaveup":
                $translatorevent['attempt_completed'] = false;
                $translatorevent['attempt_success'] = false;
                break;
            case "complete":
                $translatorevent['attempt_completed'] = true;
                $translatorevent['attempt_success'] = null;
                break;
            case "gradedwrong":
                $translatorevent['attempt_completed'] = true;
                $translatorevent['attempt_success'] = false;
                $translatorevent['attempt_score_scaled'] = $submittedstate->fraction;
                $translatorevent['attempt_score_raw'] = $submittedstate->fraction * $question_attempt->maxmark;
                break;
            case "gradedpartial":
                $translatorevent['attempt_completed'] = true;
                $translatorevent['attempt_success'] = false;
                $translatorevent['attempt_score_scaled'] = $submittedstate->fraction;
                $translatorevent['attempt_score_raw'] = $submittedstate->fraction * $question_attempt->maxmark;
                break;
            case "gradedright":
                $translatorevent['attempt_completed'] = true;
                $translatorevent['attempt_success'] = true;
                $translatorevent['attempt_score_scaled'] = $submittedstate->fraction;
                $translatorevent['attempt_score_raw'] = $submittedstate->fraction * $question_attempt->maxmark;
                break;
            default:
                $translatorevent['attempt_completed'] = null;
                $translatorevent['attempt_success'] = null;
                break;
        }

        return $translatorevent;
    }

    /**
     * Add data specifc to multichoice and true/false question types to a translator event.
     * @param [String => Mixed] $translatorevent
     * @param PHPObj $question_attempt
     * @param PHPObj $question
     * @return [String => Mixed]
     */
    public function multichoice_statement($translatorevent, $question_attempt, $question) {
        $choices = [];
        foreach ($question->answers as $answerId => $answer) {
            $choices['moodle_quiz_question_answer_'.$answerId] = strip_tags($answer->answer);
        }

        // If there are answers, assume multiple choice until proven otherwise.
        $translatorevent['interaction_type'] = 'choice';
        $translatorevent['interaction_choices'] = $choices;

        $responses = [];

        // We can't simply explode $question_attempt->responsesummary because responses may contain "; ".
        foreach ($choices as $answerId => $choice) {
            $choicePos = strpos($question_attempt->responsesummary, $choice);
            if (
                // Check if choice is contained in the learner's response
                !($choicePos === false)
                // Check choice is prefixed with "; " or at start of string.
                && (
                    ($choicePos == 0)
                    || (substr($question_attempt->responsesummary, $choicePos - 2, 2) == "; ")
                )
                // Check choice is follow by "; " or at end of string.
                && (
                    ($choicePos == strlen($question_attempt->responsesummary) -  strlen($choice))
                    || (substr($question_attempt->responsesummary, $choicePos + strlen($choice), 2) == "; ")
                )
            ) {
                array_push($responses, $answerId);
            }
        }

        if ($responses != []) {
            $translatorevent['attempt_response'] = implode('[,]', $responses);
        }

        $correctResponses = [];
        foreach ($choices as $answerId => $choice) {
            if (!(strpos($question_attempt->rightanswer, $choice) === false)) {
                array_push($correctResponses, $answerId);
            }
        }
        $translatorevent['interaction_correct_responses'] = [implode('[,]', $correctResponses)];

        // Special handling of true-false question type (some overlap with multichoice).
        if ($question->qtype == 'truefalse') {
            $translatorevent['interaction_type'] = 'true-false';
            $translatorevent['interaction_choices'] = null;

            if ($question_attempt->responsesummary == 'True') {
                $translatorevent['attempt_response'] = 'true';
            } else if ($question_attempt->responsesummary == 'False') {
                $translatorevent['attempt_response'] = 'false';
            }

            if ($question_attempt->rightanswer == 'True') {
                $translatorevent['interaction_correct_responses'] = ['true'];
            } else if ($question_attempt->rightanswer == 'False') {
                $translatorevent['interaction_correct_responses'] = ['false'];
            }
        }
        return $translatorevent;
    }

    /**
     * Get pertient data from the last recorded step of a learners interactions within a question attempt.
     * @param PHPObj $question_attempt
     * @return [String => Mixed]
     */
    private function get_last_state($question_attempt) {

        // Default placeholder to -1 so that the first item we check will always be greater than the placeholder.
        $sequencenumber = -1;

        // Default state in case there are no steps.
        $state = (object)[
            "state" => "todo",
            "timestamp" => null
        ];

        // Cycle through steps to find the last one (the one with the highest sequence number).
        foreach ($question_attempt->steps as $stepid => $step) {
            if ($step->sequencenumber > $sequencenumber) {

                // Now this step has the highest sequence number we've seen.
                $sequencenumber = $step->sequencenumber;
                $state = (object)[
                    "state" => $step->state,
                    "timestamp" => $step->timecreated,
                    "fraction" => $step->fraction
                ];
            }
        }
        return $state;
    }
}