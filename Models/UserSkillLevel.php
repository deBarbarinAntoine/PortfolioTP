<?php

namespace App\Models;

/**
 */
enum UserSkillLevel: string
{
    case DEBUTANT = 'beginner';
    case INTERMEDIATE = 'medium';
    case ADVANCED = 'advanced';
    case EXPERT = 'expert';
}