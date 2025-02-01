<?php

namespace App\Models;

// The Logger class is responsible for logging messages with various levels of severity.
// It creates log messages with timestamps, locations, and specific log levels (e.g., debug, info, etc.).
class Logger
{

    // The severity level of the log message (debug, info, etc.).
    private Level $level;

    // The actual log message to be recorded.
    private string|array $message;

    // The date and time when the log message was created.
    private string $datetime;

    // The location or context where the log message originated.
    private string $location;

    /**
     * Constructor for the Logger class, which initializes the log properties.
     * This is private since Logger objects are created using the static `log` method.
     *
     * @param string|array $message The log message content.
     * @param string $datetime The date and time of the log.
     * @param string $location The context or file where the log originates.
     * @param Level $level The severity level of the log (default is Level::ERROR).
     */
    private function __construct(string|array $message, string $datetime, string $location, Level $level = Level::ERROR)
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
     * @param string|array $message The content of the log message.
     * @param string $location The context or file where the log originates.
     * @param Level $level The severity level of the log (default is Level::ERROR).
     *
     * Debug logs are only printed if the 'ENVIRONMENT' variable
     * in the environment is set to 'development'. Logs at other levels are always printed.
     */
    public static function log(string|array $message, string $location, Level $level = Level::ERROR): void
    {
        $datetime = new \DateTime(); // Create a new DateTime object for the current timestamp.
        $logger = new self($message, $datetime->format("Y-m-d H:i:s"), $location, $level); // Initialize a Logger object.

        // Check if the log level is not debug. Logs with levels other than debug are always printed.
        if ($logger->level !== Level::DEBUG) {
            $logger->print();

        // If the log level is debug, only print if the 'ENVIRONMENT' variable is set to 'development'.
        } else if (isset($_ENV['ENVIRONMENT']) && $_ENV['ENVIRONMENT'] === 'development') {
            $logger->print();
        }
    }

    /**
     * Outputs the log message in JSON format to the PHP error log.
     * This method formats the log message with relevant data.
     */
    private function print(): void
    {
        // Prepare the message content by processing $this->message.
        $message = '';

        // Check if the message is a string. If yes, use it directly. 
        if (gettype($this->message) === 'string') {
            $message = $this->message;
        } else {

            // If the message is an indexed array, join its elements with commas.
            if (array_is_list($this->message)) {
                $message = implode(", ", $this->message);
            } else {

                // If the message is an associative array, convert key-value pairs to a string format.
                $elements = [];
                foreach ($this->message as $key => $value) {
                    $elements[] = "$key: $value";
                }
                $message = implode(", ", $elements);
            }
        }

        // Log the formatted message with the timestamp, level, and location in JSON format.
        error_log("{ \"time\": \"$this->datetime\", \"level\": \"$this->level\", \"location\": \"$this->location\", \"message\": \"$message\" }");
    }
}

// Represents various logging levels indicating the severity of the log messages.
enum Level: string
{
    case DEBUG = 'debug';       // Used for detailed debug information.
    case INFO = 'info';        // Used for informational messages.
    case WARNING = 'warning';  // Used for warning messages that are not errors.
    case ERROR = 'error';      // Used for error messages.
}