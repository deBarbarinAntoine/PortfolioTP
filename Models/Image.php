<?php

namespace App\Models;

use DateTime;

/**
 * Class Image
 *
 * Represents an image object with metadata such as path, name, and upload timestamp.
 */
class Image
{
    /**
     * The unique identifier of the image.
     *
     * @var int
     */
    private int $id;

    /**
     * The file path where the image is stored.
     *
     * @var string
     */
    private string $path;

    /**
     * The name of the image file.
     *
     * @var string
     */
    private string $name;

    /**
     * The timestamp of when the image was uploaded.
     *
     * @var DateTime
     */
    private DateTime $uploaded_at;

    /**
     * Gets the unique identifier of the image.
     *
     * @return int The image ID.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Gets the file path of the image.
     *
     * @return string The image file path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Sets the file path of the image.
     *
     * @param string $path The new file path of the image.
     * @return void
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * Gets the name of the image.
     *
     * @return string The image name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name of the image.
     *
     * @param string $name The new name of the image.
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Gets the upload timestamp of the image.
     *
     * @return DateTime The upload timestamp.
     */
    public function getUploadedAt(): DateTime
    {
        return $this->uploaded_at;
    }

    /**
     * Sets the upload timestamp of the image.
     *
     * @param DateTime $uploaded_at The new upload timestamp.
     * @return void
     */
    public function setUploadedAt(DateTime $uploaded_at): void
    {
        $this->uploaded_at = $uploaded_at;
    }

    /**
     * Constructs an Image object with the specified properties.
     *
     * @param int $id The unique identifier of the image.
     * @param string $path The file path of the image.
     * @param string $name The name of the image.
     * @param DateTime $uploaded_at The upload timestamp of the image.
     */
    public function __construct(int $id, string $path, string $name, DateTime $uploaded_at)
    {
        $this->id = $id;
        $this->uploaded_at = $uploaded_at;
        $this->path = $path;
        $this->name = $name;
    }

    /**
     * Factory method to create a new Image object with default settings.
     *
     * @param string $name The name of the new image.
     * @return Image A new instance of the Image class.
     */
    public static function new(string $name): Image
    {
        return new Image(
            -1,
            'public/img/',
            $name,
            new DateTime()
        );
    }
}