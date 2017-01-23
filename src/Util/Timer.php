<?php

namespace Backup\Util;

class Timer
{
    /** @var  \DateTime */
    protected $time;

    /** @var  \DateTime */
    protected $endTime;

    public function __construct()
    {
        $this->start();
    }

    public function start()
    {
        $this->time = new \DateTime;
        $this->endTime = null;
    }

    public function end()
    {
        $this->endTime = new \DateTime;
    }

    public function getInterval()
    {
        $endTime = $this->endTime ?? new \DateTime();
        return $this->time->diff($endTime);
    }

    public function getIntervalEnglish()
    {
        $output = '';

        $days = $this->getInterval()->d;
        if($days > 0){
            $output .= sprintf('%s days ', $days);
        }

        $hours = $this->getInterval()->h;
        if($hours > 0){
            $output .= sprintf('%s hours ', $hours);
        }

        $minutes = $this->getInterval()->i;
        if($minutes > 0){
            $output .= sprintf('%s minutes ', $minutes);
        }

        $seconds = $this->getInterval()->s;
        $output .= sprintf('%s seconds', $seconds);

        return $output;
    }
}