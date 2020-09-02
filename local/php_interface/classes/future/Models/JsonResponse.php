<?php

namespace Future\Models;

class JsonResponse
{
    /**
     * @var array
     */
    private $data;
    /**
     * @var int
     */
    private $status;

    public function __construct(array $data, $status = 200)
    {
        header("Content-Type: application/json");

        $this->data = $data;
        $this->status = new HttpStatus($status);
    }

    public function __toString()
    {
        return json_encode($this->data, JSON_UNESCAPED_UNICODE);
    }
}
