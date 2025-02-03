<?php

namespace App\Models;

use DateTime;
use PDOException;

/**
 * Represents a project within the application.
 *
 * This class manages projects containing attributes such as title, description,
 * external links, visibility status, associated images, and timestamps for creation
 * and their last updates. It provides methods to create, update, and fetch project
 * details, including counts of projects within specific date ranges.
 */
class Project
{

    /**
     * Unique identifier for the project.
     *
     * @var int
     */
    private int $id;

    /**
     * Title of the project.
     *
     * @var string
     */
    private string $title;

    /**
     * Description of the project.
     *
     * @var string
     */
    private string $description;

    /**
     * External link related to the project.
     *
     * @var string
     */
    private string $external_link;

    /**
     * Visibility status of the project (e.g., public or private).
     *
     * @var Visibility
     */
    private Visibility $visibility;

    /**
     * Array of images associated with the project.
     *
     * @var array
     */
    private array $images;

    /**
     * Timestamp of when the project was created.
     *
     * @var DateTime
     */
    private DateTime $created_at;

    /**
     * Timestamp of when the project was last updated.
     *
     * @var DateTime
     */
    private DateTime $updated_at;

    /**
     * Get the ID of the project.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the title of the project.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the title of the project.
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get the description of the project.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the description of the project.
     *
     * @param string $description
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get the external link of the project.
     *
     * @return string
     */
    public function getExternalLink(): string
    {
        return $this->external_link;
    }

    /**
     * Set the external link of the project.
     *
     * @param string $external_link
     * @return void
     */
    public function setExternalLink(string $external_link): void
    {
        $this->external_link = $external_link;
    }

    /**
     * Get the visibility of the project.
     *
     * @return Visibility
     */
    public function getVisibility(): Visibility
    {
        return $this->visibility;
    }

    /**
     * Set the visibility of the project.
     *
     * @param Visibility $visibility
     * @return void
     */
    public function setVisibility(Visibility $visibility): void
    {
        $this->visibility = $visibility;
    }

    /**
     * Get the images associated with the project.
     *
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * Set the images associated with the project.
     *
     * @param array $images
     * @return void
     */
    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    /**
     * Get the creation timestamp of the project.
     *
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    /**
     * Update the creation timestamp of the project.
     *
     * @param DateTime $date
     * @return void
     */
    public function setCreatedAt(DateTime $date = new DateTime()): void
    {
        $this->created_at = $date;
    }

    /**
     * Get the last updated timestamp of the project.
     *
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }

    /**
     * Update the last updated timestamp.
     *
     * @param DateTime $date
     * @return void
     */
    public function setUpdatedAt(DateTime $date = new DateTime()): void
    {
        $this->updated_at = $date;
    }

    /**
     * Private constructor to initialize the project instance.
     *
     * @param int $id
     * @param string $title
     * @param string $description
     * @param string $external_link
     * @param Visibility $visibility
     * @param DateTime $created_at
     * @param DateTime $updated_at
     * @param array $images
     */
    private function __construct(
        int        $id,
        string     $title,
        string     $description,
        string     $external_link,
        Visibility $visibility,
        DateTime   $created_at,
        DateTime   $updated_at,
        array      $images = []
    )
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->external_link = $external_link;
        $this->visibility = $visibility;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->images = $images;
    }

    /**
     * Factory method to create a new project instance.
     *
     * @param string $title
     * @param string $description
     * @param string $external_link
     * @param Visibility $visibility
     * @param array $images
     * @return static
     */
    public static function new(
        string     $title,
        string     $description,
        string     $external_link = '',
        Visibility $visibility = Visibility::PRIVATE,
        array      $images = []
    ): static
    {
        return new self(
            -1,
            $title,
            $description,
            $external_link,
            $visibility,
            new DateTime(),
            new DateTime(),
            $images,
        );
    }

    /**
     * Create a new project record in the database.
     *
     * This method saves the current project instance data into the database using
     * a CRUD object for the `projects` table. It also handles the creation of
     * associated image records in the `project_images` table, if any are available.
     *
     * @return int The ID of the newly created project on success, or -1 if an error occurs.
     *
     * @throws PDOException If the database operation fails, it is logged and handled.
     */
    public function create(): int
    {
        // Initialize CRUD objects for project and related images.
        $project_crud = new Crud('projects');
        $project_image_crud = new Crud('project_images');

        try {
            // Create a new project record and fetch the generated ID.
            $id = $project_crud->create(
                [
                    'title' => $this->title,
                    'description' => $this->description,
                    'external_link' => $this->external_link,
                    'visibility' => $this->visibility
                ]
            );

            // Insert image records associated with the project, if any exist.
            foreach ($this->images as $image) {
                $project_image_crud->create(
                    [
                        'project_id' => $id,
                        'image_path' => $image->getPath() . $image->getName(),
                    ]
                );
            }

        } catch (PDOException $e) {

            // LOGGING -> Log any exceptions that occur during the operation.
            Logger::log($e->getMessage(), __METHOD__);

            // Return -1 to indicate a failure in project creation.
            return -1;
        }

        // Return the newly created project ID upon success.
        return $id;
    }

    /**
     * Retrieve the total count of all projects in the database.
     *
     * @return mixed The total number of projects or null if no projects exist.
     */
    public static function getCountAll(): mixed
    {
        $project_crud = new Crud('projects');
        return $project_crud->findSingleValueBy();
    }

    /**
     * Get the count of projects created within the last 24 hours.
     *
     * @return mixed The count of projects created in the past day or null if none exist.
     */
    public static function getCountLastProject(): mixed
    {
        $project_crud = new Crud('projects');
        $conditions = [
            'created_at' => 'NOW() - INTERVAL 24 HOUR'
        ];
        return $project_crud->findSingleValueBy($conditions);
    }
}