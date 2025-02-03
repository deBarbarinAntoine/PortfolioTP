<?php

namespace App\Models;


use DateTime;

class Project {

    private int $id;
    private string $title;
    private string $details;
    private string $external_link;
    private string $private;
    private DateTime $created_at;
    private DateTime $updated_at;


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