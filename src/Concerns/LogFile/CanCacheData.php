<?php

namespace Commando1251\LogViewer\Concerns\LogFile;

use Carbon\CarbonInterface;
use Commando1251\LogViewer\Facades\Cache;
use Commando1251\LogViewer\Utils\GenerateCacheKey;
use Commando1251\LogViewer\Utils\Utils;

trait CanCacheData
{
    protected function indexCacheKeyForQuery(string $query = ''): string
    {
        return GenerateCacheKey::for($this, Utils::shortMd5($query).':index');
    }

    public function clearCache(): void
    {
        foreach ($this->getMetadata('related_indices', []) as $indexIdentifier => $indexMetadata) {
            $this->index($indexMetadata['query'])->clearCache();
        }

        foreach ($this->getRelatedCacheKeys() as $relatedCacheKey) {
            Cache::forget($relatedCacheKey);
        }

        Cache::forget($this->metadataCacheKey());
        Cache::forget($this->relatedCacheKeysKey());

        $this->index()->clearCache();
    }

    public function cacheSize(): int
    {
        $size = 0;

        foreach ($this->getMetadata('related_indices', []) as $indexIdentifier => $indexMetadata) {
            $size += $this->index($indexMetadata['query'])->cacheSize();
        }

        foreach ($this->getRelatedCacheKeys() as $relatedCacheKey) {
            $size += strlen(serialize(Cache::get($relatedCacheKey)));
        }

        $size += strlen(serialize(Cache::get($this->metadataCacheKey())));
        $size += strlen(serialize(Cache::get($this->relatedCacheKeysKey())));

        $size += $this->index()->cacheSize();

        return $size;
    }

    protected function cacheTtl(): CarbonInterface
    {
        return now()->addWeek();
    }

    protected function cacheKey(): string
    {
        return GenerateCacheKey::for($this);
    }

    protected function relatedCacheKeysKey(): string
    {
        return GenerateCacheKey::for($this, 'related-cache-keys');
    }

    public function addRelatedCacheKey(string $key): void
    {
        $keys = $this->getRelatedCacheKeys();
        $keys[] = $key;
        Cache::put(
            $this->relatedCacheKeysKey(),
            array_unique($keys),
            $this->cacheTtl()
        );
    }

    protected function getRelatedCacheKeys(): array
    {
        return array_merge(
            Cache::get($this->relatedCacheKeysKey(), []),
            [
                $this->indexCacheKeyForQuery(),
                $this->indexCacheKeyForQuery().':last-scan',
            ]
        );
    }

    protected function metadataCacheKey(): string
    {
        return GenerateCacheKey::for($this, 'metadata');
    }

    protected function loadMetadataFromCache(): array
    {
        return Cache::get($this->metadataCacheKey(), []);
    }

    protected function saveMetadataToCache(array $data): void
    {
        Cache::put($this->metadataCacheKey(), $data, $this->cacheTtl());
    }
}
