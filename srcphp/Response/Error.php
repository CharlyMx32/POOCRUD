<?php

namespace proyecto\Response;

class Error
{
    private $message;
    
    public function __construct($message)
    {
        $this->message = $message;
    }
    
    public function send()
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $this->message
        ]);
        exit();
    }
}
