<?php namespace logstore_xapi\translator\events;

class attempt_reviewed extends attempt_started {
    /**
     * Reads data for an event.
     * @param [String => Mixed] $opts
     * @return [String => Mixed]
     * @override AttemtStarted
     */
    public function read(array $opts) {
        $seconds = $opts['attempt']->timefinish - $opts['attempt']->timestart;
        $duration = "PT".(string) $seconds."S";
        $scoreraw = (float) ($opts['attempt']->sumgrades ?: 0);
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
        else {
            $scorescaled = $scoreraw / $scoremin;
        }
        return [array_merge(parent::read($opts)[0], [
            'recipe' => 'attempt_completed',
            'attempt_score_raw' => $scoreraw,
            'attempt_score_min' => $scoremin,
            'attempt_score_max' => $scoremax,
            'attempt_score_scaled' => $scorescaled,
            'attempt_success' => $success,
            'attempt_completed' => $opts['attempt']->state === 'finished',
            'attempt_duration' => $duration,
        ])];
    }

}