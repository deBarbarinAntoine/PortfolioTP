<?php

namespace App\Models;


class Project {

    public static function getCountAll(): mixed
    {
        $count_project_crud = new Crud('projects');
        return $count_project_crud->findSingleValueBy();
    }

    public static function getCountLastProject(): mixed
    {
        $count_project_crud = new Crud('projects');
        $conditions = [
            'created_at' => 'NOW() - INTERVAL 24 HOUR'
        ];
        return $count_project_crud->findSingleValueBy($conditions);
    }
}