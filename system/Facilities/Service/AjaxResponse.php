<?php

namespace CodeHuiter\Facilities\Service;

use CodeHuiter\Core\Request;
use CodeHuiter\Core\Response;

interface AjaxResponse
{
    /**
     * @param Request $request
     * @return bool
     */
    public function isAjaxRequested(Request $request): bool;

    /**
     * @param Request $request
     * @return bool
     */
    public function isBodyAjax(Request $request): bool;

    /**
     * Start events
     * @return self
     */
    public function events(): AjaxResponse;

    /**
     * End events and render response (And can append result to response)
     * @param Response|null $response
     * @return string
     */
    public function render(?Response $response = null): string;

    /**
     * User redirect
     * @param string $url
     * @return self
     */
    public function location(string $url): AjaxResponse;

    /**
     * Insert content into selector block
     * @param string $selector
     * @param string $content
     * @return self
     */
    public function insert(string $selector, string $content): AjaxResponse;

    /**
     * Append content into selector block
     * @param string $selector
     * @param string $content
     * @return self
     */
    public function append(string $selector, string $content): AjaxResponse;

    /**
     * Show success message
     * @param string $message
     * @return self
     */
    public function successMessage(string $message): AjaxResponse;

    /**
     * Show error message
     * @param string $message
     * @return self
     */
    public function errorMessage(string $message): AjaxResponse;

    /**
     * Indication fields with errors in form
     * @param string $field
     * @return self
     */
    public function incorrect(string $field): AjaxResponse;

    /**
     * Replace form content in form
     * @param string $content
     * @return self
     */
    public function formReplace(string $content): AjaxResponse;

    /**
     * Reload page
     * @return self
     */
    public function reload(): AjaxResponse;

    /**
     * Close all popups
     * @return self
     */
    public function closePopups(): AjaxResponse;

    /**
     * @param string $content
     * @param string $name
     * @param array $options
     * <br/> maxWidth => int
     * <br/> close => bool
     * @return self
     */
    public function openPopupWithData(string $content, string $name, array $options): AjaxResponse;

    public function clearEvents(): void;

    public function hasEvents(): bool;
}
