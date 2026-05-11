<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Database\Adapters\Sleekdb;

use Quantum\Database\Adapters\Sleekdb\Statements\Criteria;
use Quantum\Database\Adapters\Sleekdb\Statements\Reducer;
use Quantum\Database\Adapters\Sleekdb\Statements\Result;
use Quantum\Database\Adapters\Sleekdb\Statements\Model;
use Quantum\Database\Adapters\Sleekdb\Statements\Join;
use SleekDB\Exceptions\InvalidConfigurationException;
use Quantum\Database\Exceptions\DatabaseException;
use SleekDB\Exceptions\InvalidArgumentException;
use Quantum\Database\Contracts\DbalInterface;
use Quantum\Model\Exceptions\ModelException;
use Quantum\App\Exceptions\BaseException;
use SleekDB\Exceptions\IOException;
use Quantum\Model\DbModel;
use SleekDB\QueryBuilder;
use RuntimeException;
use SleekDB\Store;

/**
 * Class SleekDbal
 * @package Quantum\Database
 */
class SleekDbal implements DbalInterface
{
    use Model;
    use Result;
    use Criteria;
    use Reducer;
    use Join;

    protected bool $isNew = false;

    /**
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * @var array<string, mixed>
     */
    protected $modifiedFields = [];

    /**
     * @var array<int, array<int|string, mixed>|string>
     */
    protected array $criterias = [];

    /**
     * @var array<int, array<int|string, mixed>>
     */
    protected array $havings = [];

    /**
     * @var array<string>
     */
    protected $selected = [];

    /**
     * @var array<string>
     */
    protected $grouped = [];

    /**
     * @var array<string, string>
     */
    protected $ordered = [];

    /**
     * @var int|null
     */
    protected $offset;

    /**
     * @var int|null
     */
    protected $limit;

    /**
     * @var array<int, array<string, mixed>>
     */
    protected $joins = [];

    /**
     * @var array<int, array<int|string, mixed>|string>
     */
    protected array $rootCriterias = [];

    /**
     * @var array<string, array<int, array{0:string,1:string,2:mixed}>>
     */
    protected array $relatedCriteriasByPath = [];

    /**
     * @var array<int, string>
     */
    protected array $requiredRelatedPaths = [];

    protected bool $criteriaPrepared = false;

    /**
     * @var array<int, string>
     */
    protected array $autoSelectedRelatedRoots = [];

    /**
     * Associated model name
     */
    private ?string $modelName;

    /**
     * The database table associated with model
     */
    private string $table;

    /**
     * ID column of table
     */
    private string $idColumn;

    /**
     * Foreign keys (related class name => relation definition)
     * @var array<string, array<string, mixed>>
     */
    private array $foreignKeys;

    /**
     * Hidden fields
     * @var array<string>
     */
    public array $hidden = [];

    /**
     * ORM Model
     */
    private ?Store $ormModel = null;

    private ?QueryBuilder $queryBuilder = null;

    /**
     * Active connection
     * @var array<string, mixed>|null
     */
    private static ?array $connection = null;

    /**
     * Operators map
     * @var string[]
     */
    private array $operators = [
        '=', '!=',
        '>', '>=',
        '<', '<=',
        'IN', 'NOT IN',
        'LIKE', 'NOT LIKE',
        'BETWEEN', 'NOT BETWEEN',
    ];

    /**
     * @param array<string, array<string, mixed>> $foreignKeys
     * @param array<string> $hidden
     */
    public function __construct(
        string $table,
        ?string $modelName = null,
        string $idColumn = 'id',
        array  $foreignKeys = [],
        array  $hidden = []
    ) {
        $this->modelName = $modelName;
        $this->table = $table;
        $this->idColumn = $idColumn;
        $this->foreignKeys = $foreignKeys;
        $this->hidden = $hidden;
    }

    /**
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @inheritDoc
     * @param array<string, mixed> $config
     */
    public static function connect(array $config): void
    {
        self::$connection = $config;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @param mixed $modifiedFields
     */
    public function setModifiedFields($modifiedFields): void
    {
        $this->modifiedFields = $modifiedFields;
    }

    public function setIsNew(bool $isNew): void
    {
        $this->isNew = $isNew;
    }

    /**
     * @inheritDoc
     * @return array<string, mixed>|null
     */
    public static function getConnection(): ?array
    {
        return self::$connection;
    }

    /**
     * @inheritDoc
     */
    public static function disconnect(): void
    {
        self::$connection = null;
    }

    /**
     * @inheritDoc
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Gets the ORM model
     * @throws DatabaseException
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     * @throws BaseException
     */
    public function getOrmModel(): Store
    {
        if (!$this->ormModel) {
            if (!self::getConnection()) {
                throw DatabaseException::missingConfig('database');
            }

            $connection = self::getConnection();

            if (empty($connection['database_dir'])) {
                throw DatabaseException::incorrectConfig();
            }

            $connection['config']['primary_key'] = $this->idColumn;

            $this->ormModel = new Store($this->table, $connection['database_dir'], $connection['config']);
        }

        return $this->ormModel;
    }

    /**
     * @param array<string, mixed>|null $modelData
     */
    public function updateOrmModel(?array $modelData): void
    {
        $this->data = $modelData ?? [];
        $this->modifiedFields = $modelData ?? [];
        $this->isNew = false;
    }

    /**
     * @inheritdoc
     */
    public function truncate(): bool
    {
        try {
            $this->getOrmModel()->deleteStore();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Gets the query builder object
     * @throws BaseException
     * @throws DatabaseException
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     * @throws ModelException
     */
    public function getBuilder(): QueryBuilder
    {
        $builder = $this->queryBuilder;

        if (!$builder) {
            $builder = $this->getOrmModel()->createQueryBuilder();
            $this->queryBuilder = $builder;
        }

        if (!$this->criteriaPrepared) {
            $this->prepareCriteriaScopes();
        }

        if ($this->selected !== []) {
            $builder->select($this->buildSelectForQuery());
        }

        if ($this->joins !== []) {
            $this->applyJoins();
        }

        if ($this->rootCriterias !== []) {
            $builder->where($this->rootCriterias);
        }

        if ($this->havings !== []) {
            $builder->having($this->havings);
        }

        if ($this->grouped !== []) {
            $builder->groupBy($this->grouped);
        }

        if ($this->ordered !== []) {
            $builder->orderBy($this->ordered);
        }

        if ($this->offset) {
            $builder->skip($this->offset);
        }

        if ($this->limit) {
            $builder->limit($this->limit);
        }

        return $builder;
    }

    /**
     * Gets foreign keys
     * @return array<string, array<string, mixed>>
     */
    public function getForeignKeys(): array
    {
        return $this->foreignKeys;
    }

    /**
     * Gets the associated model name
     */
    public function getModelName(): string
    {
        return $this->modelName ?? '';
    }

    /**
     * Resets the builder state
     */
    protected function resetBuilderState(): void
    {
        $this->criterias = [];
        $this->rootCriterias = [];
        $this->relatedCriteriasByPath = [];
        $this->requiredRelatedPaths = [];
        $this->criteriaPrepared = false;
        $this->autoSelectedRelatedRoots = [];
        $this->havings = [];
        $this->selected = [];
        $this->grouped = [];
        $this->ordered = [];
        $this->offset = null;
        $this->limit = null;
        $this->joins = [];
        $this->queryBuilder = null;
    }

    /**
     * Splits collected criteria into root-store and related-join scopes.
     * Related path criteria are mapped to join paths and excluded from root where.
     */
    protected function prepareCriteriaScopes(): void
    {
        $this->rootCriterias = $this->criterias;
        $this->relatedCriteriasByPath = [];
        $this->requiredRelatedPaths = [];
        $this->criteriaPrepared = true;

        if ($this->joins === [] || $this->criterias === []) {
            return;
        }

        $joinPaths = $this->collectJoinPaths();

        if ($joinPaths === []) {
            return;
        }

        $rootCriterias = [];
        $foundRelated = false;
        $hasOr = false;

        foreach ($this->criterias as $criteria) {
            if (is_string($criteria)) {
                $hasOr = $hasOr || strtoupper($criteria) === 'OR';
                $rootCriterias[] = $criteria;
                continue;
            }

            if (!is_array($criteria) || !isset($criteria[0]) || !is_string($criteria[0])) {
                $rootCriterias[] = $criteria;
                continue;
            }

            $column = $criteria[0];
            $relatedMatch = $this->matchRelatedPath($column, $joinPaths);

            if ($relatedMatch === null) {
                $rootCriterias[] = $criteria;
                continue;
            }

            $foundRelated = true;
            $path = $relatedMatch['path'];
            $localColumn = $relatedMatch['column'];

            $this->relatedCriteriasByPath[$path][] = [$localColumn, $criteria[1], $criteria[2] ?? null];
            $this->requiredRelatedPaths[] = $path;
        }

        if ($foundRelated && $hasOr) {
            throw new RuntimeException(
                'SleekDB related-model criterias do not support OR combinations with root/related scopes yet.'
            );
        }

        $this->requiredRelatedPaths = array_values(array_unique($this->requiredRelatedPaths));
        $this->rootCriterias = $rootCriterias;
    }

    /**
     * Builds all accessible join paths (including nested ones) from current join chain.
     * @return array<int, string>
     */
    protected function collectJoinPaths(): array
    {
        $paths = [];
        $this->collectJoinPathsRecursive($this->joins, 0, '', $paths);
        usort($paths, static fn (string $a, string $b): int => substr_count($b, '.') <=> substr_count($a, '.'));
        return array_values(array_unique($paths));
    }

    /**
     * Recursively resolves join paths while respecting switch mode for same-level joins.
     * @param array<int, array<string, mixed>> $joins
     * @param array<int, string> $paths
     */
    protected function collectJoinPathsRecursive(array $joins, int $level, string $currentPath, array &$paths): void
    {
        if (!isset($joins[$level])) {
            return;
        }

        $nextItem = $joins[$level];
        $model = unserialize($nextItem['model']);

        if (!$model instanceof DbModel) {
            return;
        }

        $joinPath = $this->buildJoinPath($currentPath, $model->table);
        $paths[] = $joinPath;

        $switch = (bool) ($nextItem['switch'] ?? true);

        if ($switch) {
            $this->collectJoinPathsRecursive($joins, $level + 1, $joinPath, $paths);
            return;
        }

        $this->collectJoinPathsRecursive($joins, $level + 1, $currentPath, $paths);
    }

    /**
     * Matches a dotted criteria column to the deepest known join path.
     * @param array<int, string> $joinPaths
     * @return array{path:string,column:string}|null
     */
    protected function matchRelatedPath(string $column, array $joinPaths): ?array
    {
        $parts = explode('.', $column);

        if (count($parts) <= 1) {
            return null;
        }

        if ($parts[0] === $this->table) {
            return null;
        }

        foreach ($joinPaths as $path) {
            $pathParts = explode('.', $path);

            if (count($parts) <= count($pathParts)) {
                continue;
            }

            if (array_slice($parts, 0, count($pathParts)) !== $pathParts) {
                continue;
            }

            $localColumn = implode('.', array_slice($parts, count($pathParts)));

            return [
                'path' => $path,
                'column' => $localColumn,
            ];
        }

        return null;
    }

    /**
     * Removes parent rows that do not contain data on required related paths.
     * @param array<int, array<string, mixed>> $results
     * @return array<int, array<string, mixed>>
     */
    public function applyRelatedCriteriaPostFilter(array $results): array
    {
        if ($this->requiredRelatedPaths === []) {
            return $results;
        }

        $results = array_values(array_filter($results, function (array $row): bool {
            foreach ($this->requiredRelatedPaths as $path) {
                if (!$this->pathHasData($row, explode('.', $path))) {
                    return false;
                }
            }

            return true;
        }));

        if ($this->autoSelectedRelatedRoots !== []) {
            $results = $this->removeAutoSelectedRelatedRoots($results);
        }

        return $results;
    }

    /**
     * Checks whether a nested relation path exists and contains at least one result.
     * @param array<string, mixed> $row
     * @param array<int, string> $segments
     */
    protected function pathHasData(array $row, array $segments): bool
    {
        $nodes = [$row];

        foreach ($segments as $segment) {
            $nextNodes = [];

            foreach ($nodes as $node) {
                if (!array_key_exists($segment, $node)) {
                    continue;
                }

                $value = $node[$segment];

                if (!is_array($value) || $value === []) {
                    continue;
                }

                if ($this->isList($value)) {
                    foreach ($value as $item) {
                        if (is_array($item)) {
                            $nextNodes[] = $item;
                        }
                    }
                    continue;
                }

                $nextNodes[] = $value;
            }

            if ($nextNodes === []) {
                return false;
            }

            $nodes = $nextNodes;
        }

        return true;
    }

    /**
     * Determines whether the given array has sequential integer keys from zero.
     * @param array<mixed> $value
     */
    protected function isList(array $value): bool
    {
        $index = 0;

        foreach ($value as $key => $_) {
            if ($key !== $index) {
                return false;
            }
            $index++;
        }

        return true;
    }

    /**
     * Builds a dot-notated join path from current path and next relation table.
     */
    protected function buildJoinPath(string $currentPath, string $table): string
    {
        return $currentPath !== '' ? $currentPath . '.' . $table : $table;
    }

    /**
     * Adds related roots required for post-filtering to query selection only.
     * @return array<string, mixed>
     */
    protected function buildSelectForQuery(): array
    {
        $selected = $this->selected;
        $this->autoSelectedRelatedRoots = [];

        if ($this->requiredRelatedPaths === []) {
            return $selected;
        }

        foreach ($this->requiredRelatedPaths as $path) {
            $relatedRoot = explode('.', $path)[0];

            if ($this->selectReferencesRoot($selected, $relatedRoot)) {
                continue;
            }

            $selected[] = $relatedRoot;
            $this->autoSelectedRelatedRoots[] = $relatedRoot;
        }

        return $selected;
    }

    /**
     * Checks whether current selection already references the given relation root.
     * @param array<string, mixed> $selected
     */
    protected function selectReferencesRoot(array $selected, string $root): bool
    {
        foreach ($selected as $column) {
            if (!is_string($column)) {
                continue;
            }

            if ($column === $root || strpos($column, $root . '.') === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes relation roots auto-selected only for post-filter evaluation.
     * @param array<int, array<string, mixed>> $results
     * @return array<int, array<string, mixed>>
     */
    protected function removeAutoSelectedRelatedRoots(array $results): array
    {
        foreach ($results as &$row) {
            foreach ($this->autoSelectedRelatedRoots as $root) {
                unset($row[$root]);
            }
        }
        unset($row);

        return $results;
    }
}
