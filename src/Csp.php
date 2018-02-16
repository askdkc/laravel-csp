<?php

namespace Spatie\LaravelCsp;

use Spatie\LaravelCsp\Profiles\Allows;
use Spatie\LaravelCsp\Profiles\Directive;
use Spatie\LaravelCsp\Exceptions\InvalidDirective;

class Csp
{
    use Allows;

    /** @var \Illuminate\Support\Collection */
    public static $scriptNonce;

    /** @var \Illuminate\Support\Collection */
    public static $styleNonce;

    /** @var \Illuminate\Support\Collection */
    public $profile;

    /** @var \Illuminate\Support\Collection */
    private $keys;

    public function __construct()
    {
        $this->allowsBasics();
    }

    /**
     * @param string $directive
     * @param string|array $value
     * @return \Spatie\LaravelCsp\Csp
     * @throws \Spatie\LaravelCsp\Exceptions\InvalidDirective
     */
    public function addHeader(string $directive, $value): self
    {
        if (! isset($this->keys)) {
            $this->keys = collect();
        }
        if (! isset($this->profile)) {
            $this->profile = collect();
        }

        if (! Directive::all()->contains($directive)) {
            throw InvalidDirective::notSupported($directive);
        }

        $value = collect($value)->implode(' ');

        if ($this->keys->contains($directive)) {
            $this->profile[$directive]->push($value);
        }

        if (! $this->keys->contains($directive)) {
            $this->keys->push($directive);

            $this->profile->put($directive, collect($value));
        }

        return $this;
    }

    protected function createScriptNonce(): string
    {
        self::$scriptNonce ?: collect();

        $nonce = base64_encode(random_bytes(16));

        self::$scriptNonce->push($nonce);

        return $nonce;
    }

    protected function createStyleNonce(): string
    {
        self::$styleNonce ?: collect();

        $nonce = base64_encode(random_bytes(16));

        self::$styleNonce->push($nonce);

        return $nonce;
    }
}
