<?php
namespace Framework;

use App\Blog\Repository\CategoryRepository;
use App\Framework\Database\Repository;
use Framework\Validator\ValidationError;
use Psr\Http\Message\UploadedFileInterface;

class Validator
{

    private const MIME_TYPES = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'pdf' => 'application/pdf'
    ];

    private $params;

    /**
     * @var string[]
     */
    private $errors =  [];

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Check if the fields are not empty.
     * @param string[] ...$keys
     * @return $this
     */
    public function notEmpty(string ...$keys)
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value) || empty($value)) {
                $this->addError($key, 'empty');
            }
        }
        return $this;
    }

    /**
     * Check if fields are in array.
     * @param string[] ...$keys
     * @return Validator
     */
    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                $this->addError($key, 'required');
            }
        }
        return $this;
    }

    /**
     * Check if the element is a slug.
     * @param string[] $keys
     * @return Validator
     * @internal param string $key
     */
    public function slug(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            $pattern = '/^[a-z0-9]+(-[a-z0-9]+)*$/';
            if (!is_null($value) && !preg_match($pattern, $value)) {
                $this->addError($key, 'slug');
            }
        }
        return $this;
    }

    public function dateTime(string $key, string $format = 'Y-m-d H:i:s'): self
    {
        $value = $this->getValue($key);
        $date = \DateTime::createFromFormat($format, $value);
        $errors = \DateTime::getLastErrors();
        if ($errors['error_count'] > 0 || $errors['warning_count'] > 0 || $date === false) {
            $this->addError($key, 'datetime', [$format]);
        }
        return $this;
    }

    /**
     * @param string $key
     * @param Repository|string $repository
     * @param \PDO $pdo
     * @return Validator
     */
    public function exists(string $key, string $repository, \PDO $pdo): self
    {
        $value = $this->getValue($key);
        $statement = $pdo->prepare("SELECT id FROM {$repository} WHERE id = ?");
        $statement->execute([$value]);
        if ($statement->fetchColumn() === false) {
            $this->addError($key, 'exists', [$repository]);
        }
        return $this;
    }

    /**
     *
     * Check if key is unique.
     *
     * @param string $key
     * @param Repository|string $repository
     * @param \PDO $pdo
     * @param int|null $exclude
     * @return Validator
     */
    public function unique(string $key, string $repository, \PDO $pdo, ?int $exclude = null): self
    {
        $value = $this->getValue($key);
        $query = "SELECT id FROM {$repository} WHERE $key = ?";
        $params = [$value];
        if ($exclude !== null) {
            $query .= " AND id != ?";
            $params[] = $exclude;
        }
        $statement = $pdo->prepare($query);
        $statement->execute($params);
        if ($statement->fetchColumn() !== false) {
            $this->addError($key, 'unique', [$value]);
        }
        return $this;
    }

    /**
     * Check if the file has been well uploaded
     * @param string $key
     * @return Validator
     */
    public function uploaded(string $key): self
    {
        $file = $this->getValue($key);
        if (is_null($file) || $file->getError() !== UPLOAD_ERR_OK) {
            $this->addError($key, 'uploaded');
        }

        return $this;
    }

    /**
     * Check file format.
     * @param string $key
     * @param array $extensions
     * @return Validator
     */
    public function extension(string $key, array $extensions): self
    {
        /** @var UploadedFileInterface $file */
        $file = $this->getValue($key);
        if ($file !== null && $file->getError() === UPLOAD_ERR_OK) {
            $type = $file->getClientMediaType();
            $extension = mb_strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
            $expectedType = self::MIME_TYPES[$extension] ?? null;
            if (!in_array($extension, $extensions) || $expectedType !== $type) {
                $this->addError($key, 'filetype', [join(', ', $extensions)]);
            }
        }
        return $this;
    }

    public function length(string $key, ?int $min, ?int $max = null):self
    {
        $value = $this->getValue($key);
        $length = mb_strlen($value);
        if (!is_null($min) &&
            !is_null($max) &&
            ($length < $min || $length > $max)
        ) {
            $this->addError($key, 'betweenLength', [$min, $max]);
            return $this;
        }
        if (!is_null($min) &&
            ($length < $min)
        ) {
            $this->addError($key, 'minLength', [$min]);
            return $this;
        }
        if (!is_null($max) &&
            ($length > $max)
        ) {
            $this->addError($key, 'maxLength', [$max]);
        }

        return $this;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Get errors.
     * @return ValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Add error.
     * @param string $key
     * @param string $rule
     * @param array $attributes
     */
    private function addError(string $key, string $rule, array $attributes = []): void
    {
        $this->errors[$key] = new ValidationError($key, $rule, $attributes);
    }

    private function getValue(string $key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }

        return null;
    }
}
