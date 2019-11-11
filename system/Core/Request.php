<?php

namespace CodeHuiter\Core;

interface Request
{
    /**
     * Unique ID of request
     * @return int
     */
    public function getId(): int;

    /**
     * @return string[]
     */
    public function getSegments(): array;

    public function getProtocol(): string;

    public function getMethod(): string;

    public function getDomain(): string;

    public function getUri(): string;

    public function isCli(): bool;

    public function isAJAX(): bool;

    public function isSecure(): bool;

    public function getPostAsArray(): array;

    public function getGet(string $key, string $default = ''): string;

    public function getPost(string $key, string $default = ''): string;

    public function getRequestValue(string $key, string $default = ''): string;

    public function getCookie(string $key, string $default = ''): string;

    public function getClientIP(): string;
}
