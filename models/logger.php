<?php

namespace models;


// Represents various logging levels indicating the severity of the log messages.
enum Level: string
{
    case DEBUG = 'debug';       // Used for detailed debug information.
    case INFO = 'info';        // Used for informational messages.
    case WARNING = 'warning';  // Used for warning messages that are not errors.
    case ERROR = 'error';      // Used for error messages.
}

// The Logger class is responsible for logging messages with various levels of severity.
// It creates log messages with timestamps, locations, and specific log levels (e.g., DEBUG, INFO, etc.).
class Logger
{

    // The severity level of the log message (DEBUG, INFO, etc.).
    private Level $level;

    // The actual log message to be recorded.
    private string $message;

    // The date and time when the log message was created.
    private string $datetime;

    // The location or context where the log message originated.
    private string $location;

    /**
     * Constructor for the Logger class, which initializes the log properties.
     * This is private since Logger objects are created using the static `log` method.
     *
     * @param string $message The log message content.
     * @param string $datetime The date and time of the log.
     * @param string $location The context or file where the log originates.
     * @param Level $level The severity level of the log (default is Level::ERROR).
     */
    private function __construct(string $message, string $datetime, string $location, Level $level = Level::ERROR)
    {
        $this->level = $level;
        $this->message = $message;
        $this->datetime = $datetime;
        $this->location = $location;
    }

    /**
     * Static method to create a log message.
     * It initializes a Logger object and prints the log.
     *
     * @param string $message The content of the log message.
     * @param string $location The context or file where the log originates.
     * @param Level $level The severity level of the log (default is Level::ERROR).
     */
    public static function log(string $message, string $location, Level $level = Level::ERROR): void
    {
        $datetime = new \DateTime(); // Create a new DateTime object for the current timestamp.
        $logger = new self($message, $datetime->format("Y-m-d H:i:s"), $location, $level); // Initialize a Logger object.
        $logger->print(); // Print the log message.
    }

    /**
     * Outputs the log message in JSON format to the PHP error log.
     * This method formats the log message with relevant data.
     */
    private function print(): void
    {
        error_log("{ \"time\": \"$this->datetime\", \"level\": \"$this->level\", \"location\": \"$this->location\" \"message\": \"$this->message\" }");
    }
}