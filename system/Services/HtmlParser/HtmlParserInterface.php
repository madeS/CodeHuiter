<?php

namespace CodeHuiter\Services\HtmlParser;

interface HtmlParserInterface
{
    /**
     * @param string|null $html
     * @return HtmlParserInterface
     */
    public function load(?string $html): HtmlParserInterface;

    /**
     * @param string $selector
     * @return HtmlParserInterface[]
     */
    public function find(string $selector): array;

    /**
     * @param string $selector
     * @return HtmlParserInterface
     */
    public function findOne(string $selector): HtmlParserInterface;

    /**
     * @return bool
     */
    public function exist(): bool;

    /**
     * @return string
     */
    public function content(): string;

    /**
     * @param string $key
     * @return string
     */
    public function attr(string $key): string;

    /**
     * Free resources and all children resources
     */
    public function unload(): void;
}
