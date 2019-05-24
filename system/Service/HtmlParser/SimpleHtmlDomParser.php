<?php

namespace CodeHuiter\Service\HtmlParser;

use CodeHuiter\Service\HtmlParser\SimpleHtmlDom\SimpleHtmlDom;
use CodeHuiter\Service\HtmlParser\SimpleHtmlDom\SimpleHtmlDomNode;

class SimpleHtmlDomParser implements HtmlParserInterface
{
    /**
     * @var SimpleHtmlDom|SimpleHtmlDomNode|null
     */
    protected $instance;

    /**
     * @var HtmlParserInterface[]
     */
    protected $childInstances = [];

    public function __construct($instance = null)
    {
        if ($instance !== null) {
            $this->instance = $instance;
        }
    }

    /**
     * {@inheritdoc}
     * @return HtmlParserInterface
     */
    public function load(?string $html): HtmlParserInterface
    {
        if ($this->instance) {
            $this->unload();
        }
        $this->instance = new SimpleHtmlDom($html);
        return $this;
    }

    /**
     * @param string $selector
     * @return HtmlParserInterface[]
     */
    public function find(string $selector): array
    {
        if (!$this->instance) {
            return [];
        }
        $foundInstances = $this->instance->find($selector);
        $result = [];
        foreach ($foundInstances as $foundInstance) {
            $newInstance = new SimpleHtmlDomParser($foundInstance);
            $this->childInstances[] = $newInstance;
            $result[] = $newInstance;
        }
        return $result;
    }


    /**
     * @param string $selector
     * @return HtmlParserInterface
     */
    public function findOne(string $selector): HtmlParserInterface
    {
        if (!$this->instance) {
            return new SimpleHtmlDomParser();
        }
        $foundInstances = $this->instance->find($selector);
        foreach ($foundInstances as $foundInstance) {
            $newInstance = new SimpleHtmlDomParser($foundInstance);
            $this->childInstances[] = $newInstance;
            return $newInstance;
        }
        return new SimpleHtmlDomParser();
    }

    public function exist(): bool
    {
        return $this->instance ? true : false;
    }

    /**
     * @return string
     */
    public function content(): string
    {
        if (!$this->instance || !$this->instance instanceof SimpleHtmlDomNode) {
            return '';
        }
        return $this->instance->innertext();
    }

    public function attr(string $key): string
    {
        if (!$this->instance || !$this->instance instanceof SimpleHtmlDomNode) {
            return '';
        }
        return $this->instance->$key ?? '';
    }

    /**
     * Free resources and all children resources
     */
    public function unload(): void
    {
        foreach ($this->childInstances as $childInstance) {
            $childInstance->unload();
        }
        $this->childInstances = [];

        if ($this->instance) {
            $this->instance->clear();
            unset($this->instance);
        }
    }
}