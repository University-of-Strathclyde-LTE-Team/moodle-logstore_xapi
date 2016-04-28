<?php namespace logstore_xapi\translator\events;

class assignment_graded extends module_viewed {
    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override module_viewed
     */
    public function read(array $opts) {

        $scoreraw = (float) ($opts['grade']->grade ?: 0);
        $scoremin = (float) ($opts['grade_items']->grademin ?: 0);
        $scoremax = (float) ($opts['grade_items']->grademax ?: 0);
        $scorepass = (float) ($opts['grade_items']->gradepass ?: null);
        $success = false;
        //if there is no passing score then success is unknown.
        if ($scorepass == null) {
            $success = null;
        }
        elseif ($scoreraw >= $scorepass) {
            $success = true;
        }
        //Calculate scaled score as the distance from zero towards the max (or min for negative scores).
        $scorescaled;
        if ($scoreraw >= 0) {
            $scorescaled = $scoreraw / $scoremax;
        }
        else
        {
            $scorescaled = $scoreraw / $scoremin;
        }

        return [array_merge(parent::read($opts)[0], [
            'recipe' => 'assignment_graded',
            'graded_user_id' => $opts['graded_user']->id,
            'graded_user_url' => $opts['graded_user']->url,
            'graded_user_name' => $opts['graded_user']->username,
            'grade_score_raw' => $scoreraw,
            'grade_score_min' => $scoremin,
            'grade_score_max' => $scoremax,
            'grade_score_scaled' => $scorescaled,
            'grade_success' => $success,
            'grade_completed' => true,
            'grade_comment' => strip_tags($opts['grade_comment']),
        ])];
    }
}