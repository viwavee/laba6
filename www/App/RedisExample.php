<?php

namespace App;

class RedisExample
{
    private $socket;

    public function __construct()
    {
        // Подключаемся к Redis через TCP сокет (встроенный функционал PHP, без зависимостей)
        $this->socket = fsockopen('redis', 6379, $errno, $errstr, 5);
        if (!$this->socket) {
            throw new \Exception("Redis connection error: $errstr ($errno)");
        }
    }

    public function setValue($key, $value)
    {
        // Redis RESP protocol: *3\r\n$3\r\nSET\r\n$len\r\nkey\r\n$len\r\nvalue\r\n
        $cmd = "*3\r\n\$3\r\nSET\r\n\${$this->strlen($key)}\r\n{$key}\r\n\${$this->strlen($value)}\r\n{$value}\r\n";
        fwrite($this->socket, $cmd);
        return $this->readResponse();
    }

    public function getValue($key)
    {
        // Redis RESP protocol: *2\r\n$3\r\nGET\r\n$len\r\nkey\r\n
        $cmd = "*2\r\n\$3\r\nGET\r\n\${$this->strlen($key)}\r\n{$key}\r\n";
        fwrite($this->socket, $cmd);
        return $this->readResponse();
    }

    private function strlen($str)
    {
        return strlen($str);
    }

    private function readResponse()
    {
        $line = fgets($this->socket);
        if ($line === false) {
            return null;
        }
        $type = $line[0];
        $data = substr($line, 1, -2);

        switch ($type) {
            case '+': // Simple string
                return $data;
            case '-': // Error
                return "ERROR: $data";
            case ':': // Integer
                return (int)$data;
            case '$': // Bulk string
                if ($data == -1) {
                    return null;
                }
                $len = (int)$data;
                $value = fread($this->socket, $len + 2);
                return substr($value, 0, -2);
            case '*': // Array
                $count = (int)$data;
                if ($count == -1) {
                    return null;
                }
                $result = [];
                for ($i = 0; $i < $count; $i++) {
                    $result[] = $this->readResponse();
                }
                return $result;
            default:
                return $line;
        }
    }

    public function __destruct()
    {
        if ($this->socket) {
            fclose($this->socket);
        }
    }
}
