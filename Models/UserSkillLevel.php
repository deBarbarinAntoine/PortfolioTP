<?php

namespace App\Models;

/**
 */
enum UserSkillLevel: string
{
    case DEBUTANT = 'débutant';
    case INTERMEDIATE = 'intermédiaire';
    case ADVANCED = 'avancé';
    case EXPERT = 'expert';
}