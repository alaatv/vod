<?php

namespace App\Classes;

use Illuminate\Http\Exceptions\HttpResponseException;

class Nullable
{
    private $result;

    private $data = [];

    /**
     * Nullable constructor.
     *
     * @param $result
     * @param $data
     */
    public function __construct($result, $data)
    {
        $this->result = $result;
        $this->data = $data;
    }

    public function otherwise($response)
    {
        $this->orFailWith($response);
    }

    /**
     * @param $response
     *
     * @return mixed
     */
    public function orFailWith($response)
    {
        return $this->getValue(function () use ($response) {
            if (is_callable($response)) {
                $response = call_user_func($response, ...(array) $this->data);
            }

            throw new HttpResponseException($response);
        });
    }

    /**
     * @param $default
     *
     * @return mixed
     */
    public function getValue($default)
    {
        if (!is_null($this->result) && $this->result !== false) {
            return $this->result;
        }

        if (is_null($default)) {
            return optional();
        } else {
            if (is_callable($default)) {
                return call_user_func($default);
            }
        }

        return $default;
    }

    public function then($response)
    {
        if ($this->result) {
            throw new HttpResponseException($response);
        }
    }
}
