<?php

namespace App\Controllers;
use App\Models\Skill;

class SkillController
{

    public function getSkillsNumber()
    {
        return Skill::getCountAll();
    }

    public function createSkill(mixed $name, mixed $description): int
    {
        $skill = Skill::new($name, $description);
        return $skill->create($name,$description);
    }

    public function updateSkill(mixed $id, mixed $name, mixed $description): int
    {
        $skill = Skill::get($id);
        $skill->setName($name);
        $skill->setDescription($description);
        return $skill->update();
    }

    public function deleteSkill(mixed $skill_id): int
    {
        $skill = Skill::get($skill_id);
        return $skill->delete($skill_id);
    }

}