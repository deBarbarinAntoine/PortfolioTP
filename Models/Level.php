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
    case DEBUG = 'debug';       // Used for detailed debug information.
    case INFO = 'info';        // Used for informational messages.
    case WARNING = 'warning';  // Used for warning messages that are not errors.
    case ERROR = 'error';      // Used for error messages.
}