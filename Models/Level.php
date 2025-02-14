<?php

namespace App\Models;

/**
 * Represents the severity levels for log messages used by the Logger class.
 *
 * The `Logger` class uses this enum to classify log messages by severity:
 * - `DEBUG`: Detailed debug information.
 * - `INFO`: Informational messages.
 * - `WARNING`: Non-critical issues that should be reviewed.
 * - `ERROR`: Errors requiring immediate attention.
 */
enum Level: string
{
    case DEBUG = 'DEBUG';       // Used for detailed debug information.
    case INFO = 'INFO';        // Used for informational messages.
    case WARNING = 'WARNING';  // Used for warning messages that are not errors.
    case ERROR = 'ERROR';      // Used for error messages.
}