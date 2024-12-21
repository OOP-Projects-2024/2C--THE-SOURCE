<?php
// File: src/Common.php

class Common {
    protected function generateResponse($data, $status, $message, $code) {
        return [
            'data' => $data,
            'status' => $status,
            'message' => $message,
            'code' => $code
        ];
    }
}