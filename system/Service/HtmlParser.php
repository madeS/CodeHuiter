<?php

namespace CodeHuiter\Service;

interface HtmlParser
{
    /**
     * @param string|null $html
     * @return HtmlParser
     */
    public function load(?string $html): HtmlParser;

    /**
     * @param string $selector
     * @return HtmlParser[]
     */
    public function find(string $selector): array;

    /**
     * @param string $selector
     * @return HtmlParser
     */
    public function findOne(string $selector): HtmlParser;

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
