<?php

namespace App\Models;

use DateMalformedStringException;
use DateTime;
use Exception;

/**
 * Represents a project within the application.
 *
 * This class manages projects containing attributes such as title, description,
 * external links, visibility status, associated images, and timestamps for creation
 * and their last updates. It provides methods to create, update, and fetch project
 * details, including counts of projects within specific date ranges.
 */
class Project implements ICrud
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
     * Get the visibility of the project in string format.
     *
     * @return string the Visibility
     */
    public function getVisibilityStr(): string
    {
        return $this->visibility->value;
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
     */
    public function create(): int
    {
        // Initialize CRUD objects for project and related images.
        $project_crud = new Crud('projects');
        $project_image_crud = new Crud('project_images');

        // Create a new project record and fetch the generated ID.
        $id = $project_crud->create(
            [
                'title' => $this->title,
                'description' => $this->description,
                'external_link' => $this->external_link,
                'visibility' => $this->visibility->value
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

        // Return the newly created project ID upon success.
        return $id;
    }

    /**
     * Update the project's details and associated images in the database.
     *
     * This method updates the project's attributes in the `projects` table, using
     * the `Crud` class. It also updates the records for associated images in the
     * `project_images` table, if any images exist.
     *
     * @return int The total number of rows affected by the updates to both the project
     *             and the associated images.
     */
    public function update(): int
    {
        // Initialize CRUD objects for managing the `projects` table and images in `project_images`.
        $project_crud = new Crud('projects');
        $project_image_crud = new Crud('project_images');
    
        // Update the project's main attributes in the 'projects' table based on the current object state.
        $rows = $project_crud->updateString(
            [
                'title' => $this->title,
                'description' => $this->description,
                'external_link' => $this->external_link,
                'visibility' => $this->visibility->value // Visibility enum value stored as a string.
            ],
            [
                'id' => $this->id // Use the project's ID to identify the record to update.
            ]
        );
    
        // Initialize an array to store the number of affected rows for each image update.
        $img_rows = [];

        // Loop through each image associated with the project and update its details in `project_images`.
        foreach ($this->images as $image) {
            var_dump($this->images);
            $img_rows[] = $project_image_crud->create(
                [
                    'project_id' => $this->id, // Associate the image with the current project.
                    'image_path' => $image // image path.
                ]
            );
        }
    
        // Return the total number of rows affected across the project and image update operations.
        return $rows + array_sum($img_rows);
    }

    /**
     * Delete a project record from the database.
     *
     * This method removes the project entry with the specified ID from
     * the 'projects' table using the CRUD utility.
     *
     * @param int $id The unique identifier of the project to delete.
     * @return int The number of rows affected by the deletion. Returns 1 on success, or 0 if no record is found.
     */
    static function delete(int $id): int
    {
        // Create a new CRUD instance for the 'projects' table.
        $project_crud = new Crud('projects');

        // Execute the delete operation, specifying the ID of the project to remove.
        return $project_crud->delete(
            [
                'id' => $id // Match records by ID.
            ]
        );
    }

    /**
     * Retrieve a single project record from the database along with its associated images.
     *
     * This method fetches the project record by ID from the 'projects' table using the CRUD object,
     * and retrieves associated image records by joining the 'project_images' table.
     * The method currently returns the raw database result.
     *
     * @param int $id The unique identifier of the project to retrieve.
     * @return Project|null An array containing project data and associated images, or null if no record is found.
     * @throws DateMalformedStringException
     */
    public static function get(int $id): ?self
    {
        // Create a new CRUD instance for the 'projects' table with the alias 'p'.
        $project_crud = new Crud('projects p');

        // Fetch project details and associated images from the database using a left join.
        $results = $project_crud->findBy(
            conditions: ['p.id' => $id],  // Use alias for consistency
            columns: 'p.id, p.title, p.description, p.external_link, p.visibility, p.created_at, p.updated_at, pi.id as image_id, pi.image_path, pi.uploaded_at',
            joins: [
                [
                    'type' => 'left',
                    'table' => 'project_images pi',
                    'on' => 'p.id = pi.project_id'
                ]
            ]
        );

        // If no project is found, return null
        if (!$results) {
            return null;
        }

        // Extract project details from the first row
        $firstRow = $results;

        // Convert visibility to Enum
        $visibility = Visibility::tryFrom($firstRow['visibility']) ?? Visibility::PRIVATE;

        // Convert DateTime fields
        $createdAt = new DateTime($firstRow['created_at']);
        $updatedAt = new DateTime($firstRow['updated_at']);

        // Extract images
        $images = array_map(fn($row) => [
            'id' => $row['image_id'],
            'path' => $row['image_path'],
            'uploaded_at' => $row['uploaded_at'],
        ], array_filter($results, fn($row) => !empty($row['image_id'])));

        // Return a new instance of the project
        return new self(
            id: $id,
            title: $firstRow['title'] ?? '',
            description: $firstRow['description'] ?? '',
            external_link: $firstRow['external_link'] ?? null,
            visibility: $visibility,
            created_at: $createdAt,
            updated_at: $updatedAt,
            images: $images
        );
    }

    /**
     * Retrieve all projects from the database along with their associated images.
     *
     * This method fetches project records from the database, including their associated
     * images, and converts the results into an array of Project instances.
     *
     * @return array An array of Project objects representing all projects in the database.
     * @throws DateMalformedStringException
     */
    public static function getAll(): array
    {
        $project_crud = new Crud('projects p');

        // Execute SQL query to retrieve projects and their associated images.
        $results = $project_crud->findBy(
            conditions: [],
            columns: 'p.id, p.title, p.description, p.external_link, p.visibility, p.created_at, p.updated_at, pi.id AS image_id, pi.image_path',
            joins: [
                [
                    'type' => 'left',
                    'table' => 'project_images pi',
                    'on' => 'p.id = pi.project_id'
                ]
            ]
        );

        $projects = [];
        foreach ($results as $result) {
            $project_id = $result['id'];

            // Group images under their respective project.
            if (!isset($projects[$project_id])) {
                $projects[$project_id] = new self(
                    id: $result['id'],
                    title: $result['title'],
                    description: $result['description'],
                    external_link: $result['external_link'],
                    visibility: Visibility::from($result['visibility']),
                    created_at: new DateTime($result['created_at']),
                    updated_at: new DateTime($result['updated_at']),
                    images: []
                );
            }

            if (!empty($result['image_id']) && !empty($result['image_path'])) {

                $complete_path = $result['image_path'];

                // TODO -> encapsulate this in Image class
                $projects[$project_id]->images[] = new Image(
                    id: $result['image_id'],
                    path: dirname($complete_path),
                    name: basename($complete_path),
                    uploaded_at: new DateTime($result['uploaded_at'])
                );
            }
        }

        // TODO -> convert results in a Project entity array
        // Return grouped projects as a flattened array.
        return array_values($projects);
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

    /**
     * @throws DateMalformedStringException
     */
    public static function getPublicProjects(): array
    {
        $project_crud = new Crud('projects');
        $results = $project_crud->findAllBy(['visibility' => 'public'] , "*", null, null, null, null,null,"", true);
        $imageCrud = new Crud('project_images');
        if ($results) {
            foreach ($results as &$result) {
                $projectImages = $imageCrud->findAllBy(['project_id' => $result['id']]);
                $result['images'] = $projectImages;
            }
        }

        return self::mapResultsToProjects($results);
    }

    /**
     * @throws DateMalformedStringException
     */
    private static function mapResultsToProjects(array $results): array
    {
        $projects = [];

        foreach ($results as $result) {
            try {
                // Handling DateTime safely
                $createdAt = new DateTime($result['created_at']);
                $updatedAt = new DateTime($result['updated_at']);
            } catch (\Exception $e) {
                // Log or handle the DateTime error
                Logger::log("Invalid DateTime format: " . $e->getMessage(), __METHOD__);
                throw new DateMalformedStringException("Invalid DateTime format: " . $e->getMessage());
            }


            // Create the Project object
            $projects[] = new self(
                id: $result['id'],
                title: $result['title'],
                description: $result['description'],
                external_link: $result['external_link'],
                visibility: Visibility::from($result['visibility']),
                created_at: $createdAt,
                updated_at: $updatedAt,
                images: $result['images']
            );
        }

        return $projects;
    }

    /**
     * @throws Exception
     */
    public static function getProject(mixed $projectId): ?self
    {
        $project_crud = new Crud('projects');
        $project = $project_crud->findBy(["id" => $projectId]);

        if (!$project) {
            return null;
        }

        $project_Image_crud = new Crud('project_images');
        $projectImages = $project_Image_crud->findAllBy(["project_id" => $projectId]) ?? [];

        return new self(
            id: $projectId,
            title: $project['title'] ?? '',
            description: $project['description'] ?? '',
            external_link: $project['external_link'] ?? null,
            visibility: Visibility::tryFrom($project['visibility']) ?? Visibility::PRIVATE,
            created_at: isset($project['created_at']) ? new DateTime($project['created_at']) : null,
            updated_at: isset($project['updated_at']) ? new DateTime($project['updated_at']) : null,
            images: $projectImages,
        );
    }

    public static function createProject(string $title, string $description, string $externalLink, string $visibility): int
    {
        // Validate visibility
        $visibilityEnum = Visibility::tryFrom($visibility) ?? Visibility::PRIVATE;

        $project_crud = new Crud('projects');

        $data = [
            'title' => $title,
            'description' => $description,
            'external_link' => $externalLink,
            'visibility' => $visibilityEnum->value,
            'created_at' => new DateTime(),
            'updated_at' => new DateTime()
        ];

        return $project_crud->create($data);
    }


}