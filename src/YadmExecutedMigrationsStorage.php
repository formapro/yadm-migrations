<?php

declare(strict_types=1);

namespace Formapro\Yadm\Migration;

use MongoDB\Collection;

class YadmExecutedMigrationsStorage implements ExecutedMigrationsStorage
{
    private const KEY = 'versions';

    /**
     * @var Collection
     */
    private $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function getVersions(): array
    {
        $options['typeMap'] = ['root' => 'array', 'document' => 'array', 'array' => 'array'];

        if ($doc = $this->collection->findOne(['id' => self::KEY], $options)) {
            return $doc[self::KEY];
        }

        return [];
    }

    public function pushVersion(string $version): void
    {
        $this->collection->updateOne([
            'id' => self::KEY,
        ], [
            '$addToSet' => [
                self::KEY => $version,
            ]
        ], [
            'upsert' => true,
        ]);
    }
}
