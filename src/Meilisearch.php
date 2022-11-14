<?php

namespace Zlt\MeilisearchApiWrapper;

class Meilisearch
{
    private static ?self $instance = null;

    private ?string $domain;

    private ?string $index;

    private ?string $apiKey;

    private function __construct()
    {
        $this->domain = config('meilisearch_api_wrapper.host');
        $this->index = config('meilisearch_api_wrapper.index');
        $this->apiKey = config('meilisearch_api_wrapper.api_key');
    }

    public static function instance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @throws \Exception
     */
    protected function validate()
    {
        if (!$this->domain || !$this->index || !$this->apiKey) {
            throw new \Exception('Meilisearch config is not valid');
        }
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;
        return $this;
    }

    public function setIndex(string $document): self
    {
        $this->index = $document;
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function tasks(?int $index = null): bool|string|null
    {
        $this->validate();
        $index = $index ? '/' . $index : '';
        return shell_exec("curl \
  -X GET '{$this->domain}/tasks$index' \
  -H 'Content-Type: application/json' \
  -H 'Authorization: Bearer $this->apiKey'");
    }

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */

    /**
     * @throws \Exception
     */
    public function settings(): bool|string|null
    {
        $this->validate();
        return shell_exec("curl \
  -X GET '$this->domain/indexes/$this->index/settings' \
  -H 'Authorization: Bearer $this->apiKey'");
    }

    /**
     * @throws \Exception
     */
    public function updateSettings(array $settings): bool|string|null
    {
        $this->validate();
        $settings = json_encode($settings);
        return shell_exec("curl \
  -X PATCH '$this->domain/indexes/$this->index/settings' \
  -H 'Content-Type: application/json' \
-H 'Authorization: Bearer $this->apiKey' \
  --data-binary '$settings'");
    }

    /**
     * @throws \Exception
     */
    public function stats(): bool|string|null
    {
        $this->validate();
        return shell_exec("curl \
  -X GET '$this->domain/indexes/$this->index/stats' \
  -H 'Authorization: Bearer $this->apiKey'");
    }

    /**
     * @throws \Exception
     */
    public function import(array $data): bool|string|null
    {
        $this->validate();
        $file = tmpfile();
        $path = stream_get_meta_data($file)['uri'];
        $data = json_encode(array_values($data), JSON_UNESCAPED_UNICODE);
        fwrite($file, $data);
        $result = shell_exec("curl \
  -X POST '{$this->domain}/indexes/$this->index/documents' \
  -H 'Content-Type: application/json' \
  -H 'Authorization: Bearer $this->apiKey' --data-binary @$path");
        fclose($file);
        return $result;
    }

    /**
     * @throws \Exception
     */
    public function delete(): bool|string|null
    {
        $this->validate();
        return shell_exec("curl \
  -X DELETE '{$this->domain}/indexes/$this->index' \
  -H 'Content-Type: application/json' \
  -H 'Authorization: Bearer $this->apiKey'");
    }
}
