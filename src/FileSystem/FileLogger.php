<?php

namespace Nexus4812\TestGenerator\FileSystem;

use DateTime;

class FileLogger
{
    private string $logFile;

    public function __construct(string|null $logFile)
    {
        $this->logFile = is_null($logFile) ?
            Path::getProjectRootPath() . '/log/'. (new Datetime())->format('Y-m-d-H:i:s') .'-log.txt' :
            $logFile;
    }

    public function log(string $message): void
    {
        $dateTime = new \DateTime();
        $formattedMessage = $dateTime->format('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
        file_put_contents($this->logFile, $formattedMessage, FILE_APPEND);
    }

    public function logRequestAndResponse(float $cost, string $request, string $response): void
    {
        $this->log("Cost: " . $cost);
        $this->log("Request: " . $request);
        $this->log("Response: " . $response);
    }
}

