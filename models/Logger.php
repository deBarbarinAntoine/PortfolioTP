<?php

namespace App\Models;

/**
 * The Logger class is responsible for recording log messages of varying severity levels.
 * It allows messages to include a timestamp, location, and specific log level for consistent error tracking.
 */
class Logger
{

    /**
     * The severity level of the log message (e.g., debug, info, error).
     */
    private Level $level;

    /**
     * The actual log message to be recorded, which can be a string or an array of data.
     */
    private string|array $message;

    /**
     * A timestamp indicating when the log message was created.
     */
    private string $datetime;

    /**
     * The location or context from which the log message originated (e.g., a file or method name).
     */
    private string $location;

    /**
     * Initializes a new Logger instance with the provided message, timestamp, location, and severity level.
     * This constructor is private, as Logger instances should be created using the static `log` method only.
     *
     * @param string|array $message The content of the log message (string or structured array).
     * @param string $datetime The timestamp indicating when the log was recorded.
     * @param string $location A string describing the origin of the log (e.g., method or file name).
     * @param Level $level The severity level of the log message, with a default of Level::ERROR.
     */
    private function __construct(string|array $message, string $datetime, string $location, Level $level = Level::ERROR)
    {
        $this->level = $level;
        $this->message = $message;
        $this->datetime = $datetime;
        $this->location = $location;
    }

    /**
     * Creates and records a log message with the provided details.
     * Uses JSON format to standardize log representation and allows filtering debug messages
     * based on the environment setting.
     *
     * @param string|array $message The content of the log message, either a string or an array.
     * @param string $location Describes the context or source location of the log event.
     * @param Level $level The severity level of the log, defaulting to Level::ERROR.
     *
     * Note: debug messages will only be logged if the 'ENVIRONMENT' variable is set to 'development'.
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
     * Formats the log message into a JSON structure and outputs it to the PHP error log.
     * Handles message processing to ensure compatibility with various input types (string or array).
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