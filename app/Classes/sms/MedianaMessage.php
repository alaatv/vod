<?php

namespace App\Classes\sms;

use DateTime;

class MedianaMessage
{
    /**
     * The message content.
     *
     * @var string
     */
    public $content;

    /**
     * @var int
     */
    public $sendAt;

    public $from = null;

    public $op = null;

    public $pattern_code;

    public $input_data = [];
    public $log_data = [];

    /**
     * Create a new message instance.
     *
     * @param  string  $content
     */
    public function __construct($content = '')
    {
        $this->content = $content;
    }

    /**
     * Create a new message instance.
     *
     * @param  string  $content
     */
    public static function create($content = '')
    {
        new static($content);
    }

    /**
     * Set the message content.
     *
     * @param  string  $content
     *
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;

        return $this;
    }

    public function setInputData(array $input_data)
    {
        $this->input_data = $input_data;

        return $this;
    }

    public function setLogData(?string $referenceType, ?string $referenceId)
    {
        $this->log_data = [
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ];

        return $this;
    }

    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    public function setOp($op)
    {
        $this->op = $op;

        return $this;
    }

    /**
     * Set the message send at.
     *
     * @param  DateTime  $sendAt
     *
     * @return $this
     */
    public function sendAt($sendAt)
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    public function setPatternCode($pattern_code)
    {
        $this->pattern_code = $pattern_code;

        return $this;
    }
}
