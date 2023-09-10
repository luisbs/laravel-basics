<?php

namespace Basics\Support;

use Carbon\Carbon;

class Timespan
{
    /**
     * Start date of the timespan.
     */
    protected Carbon $start;

    /**
     * End date of the timespan.
     */
    protected ?Carbon $end = null;

    /**
     * Difference on the date.
     */
    protected ?int $diff = null;

    public static function fromDates($start, $end)
    {
        $instance = new Timespan();
        $instance->start = $start;
        $instance->end = $end;
        return $instance;
    }

    public function getDiff()
    {
        if (!is_null($this->diff)) return $this->diff;
        return $this->start->diff($this->end);
    }
}
