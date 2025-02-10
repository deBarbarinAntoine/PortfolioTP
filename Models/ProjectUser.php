<?php

namespace App\Models;

use DateTime;

/**
 * Represents the association of a user with a project and their assigned role.
 */
class ProjectUser
{
    /** @var int The unique identifier for the ProjectUser. */
    private int $id;

    /** @var ProjectRole The role of the user in the project. */
    private ProjectRole $role;

    /** @var DateTime The timestamp when the ProjectUser was created. */
    private DateTime $created_at;

    /** @var DateTime The timestamp when the ProjectUser was last updated. */
    private DateTime $updated_at;

    /** @var Project The associated project. */
    private Project $project;


    /**
     * Gets the id of the ProjectUser.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Sets the id of the ProjectUser.
     *
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the role of the user in the project.
     *
     * @return ProjectRole
     */
    public function getRole(): ProjectRole
    {
        return $this->role;
    }

    /**
     * Gets the role of the user in the project in string format.
     *
     * @return string the Project Role
     */
    public function getRoleStr(): string
    {
        return $this->role->value;
    }

    /**
     * Sets the role of the user in the project.
     *
     * @param ProjectRole $role
     * @return void
     */
    public function setRole(ProjectRole $role): void
    {
        $this->role = $role;
    }

    /**
     * Gets the creation timestamp of the ProjectUser.
     *
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    /**
     * Sets the creation timestamp of the ProjectUser.
     *
     * @param DateTime $created_at
     * @return void
     */
    public function setCreatedAt(DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * Gets the last updated timestamp of the ProjectUser.
     *
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }

    /**
     * Sets the last updated timestamp of the ProjectUser.
     *
     * @param DateTime $updated_at default `new DateTime()`
     * @return void
     */
    public function setUpdatedAt(DateTime $updated_at = new DateTime()): void
    {
        $this->updated_at = $updated_at;
    }

    /**
     * Gets the associated project.
     *
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * Sets the associated project.
     *
     * @param Project $project
     * @return void
     */
    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    /**
     * Private constructor to prevent direct instantiation.
     *
     * @param int $id
     * @param ProjectRole $role
     * @param DateTime $created_at
     * @param DateTime $updated_at
     * @param Project $project
     */
    private function __construct(
        int         $id,
        ProjectRole $role,
        DateTime    $created_at,
        DateTime    $updated_at,
        Project     $project
    )
    {
        $this->id = $id;
        $this->role = $role;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->project = $project;
    }

    /**
     * Factory method to create a new instance of ProjectUser.
     *
     * @param int $id
     * @param ProjectRole $role
     * @param DateTime $created_at
     * @param DateTime $updated_at
     * @param Project $project
     * @return ProjectUser
     */
    public static function new(
        int         $id,
        ProjectRole $role,
        DateTime    $created_at,
        DateTime    $updated_at,
        Project     $project
    ): ProjectUser
    {
        return new self($id, $role, $created_at, $updated_at, $project);
    }

    public static function getUserProject(int $user_id): array
    {
        $userProjectCrud = new Crud('project_users');

        // Initialize an empty array to hold the results for each role.
        $projectsByRole = [];

        // Loop through all the roles in the ProjectRole enum dynamically.
        foreach (ProjectRole::cases() as $role) {
            // Fetch the data for the current role and store it in the array.
            $projectsByRole[$role->value] = $userProjectCrud->findAllBy(['user_id' => $user_id, 'role' => $role->value]);
        }

        // Return the projects grouped by role
        return $projectsByRole;
    }

    public static function isOwner(string $projectId, mixed $userId): bool
    {
        $userProjectCrud = new Crud('project_users');
        $user_Project = $userProjectCrud->findBy(['project_id' => $projectId, 'user_id' => $userId]);
        if ($user_Project == null || $user_Project['role'] !== 'owner') {

            return false;
        }
        return true;
    }

    public static function isContributor(string $projectId, mixed $userId): bool
    {
        $userProjectCrud = new Crud('project_users');
        $user_Project = $userProjectCrud->findBy(['project_id' => $projectId, 'user_id' => $userId]);
        if ($user_Project == null || $user_Project['role'] !== 'contributor') {

            return false;
        }
        return true;
    }

    public static function create(string $email, string $role, string $projectId): int
    {
        $userProjectCrud = new Crud('project_users');
        $userCrud = new Crud('users');
        $user = $userCrud->findBy(['email' => $email]);
        $user_id = $user['id'];
        return $userProjectCrud->create([ 'project_id' => $projectId,'user_id'=> $user_id  ,'role' => $role ]);
    }

}