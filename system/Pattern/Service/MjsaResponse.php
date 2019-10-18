<?php

namespace CodeHuiter\Pattern\Service;

use CodeHuiter\Core\Request;
use CodeHuiter\Core\Response;

interface MjsaResponse
{
    /**
     * @param Request $request
     * @return bool
     */
    public function isMjsaRequested(Request $request): bool;

    /**
     * @param Request $request
     * @return bool
     */
    public function isBodyAjax(Request $request): bool;

    /**
     * Start events
     * @return self
     */
    public function events(): MjsaResponse;

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
    public function location(string $url): MjsaResponse;

    /**
     * Insert content into selector block
     * @param string $selector
     * @param string $content
     * @return self
     */
    public function insert(string $selector, string $content): MjsaResponse;

    /**
     * Append content into selector block
     * @param string $selector
     * @param string $content
     * @return self
     */
    public function append(string $selector, string $content): MjsaResponse;

    /**
     * Show success message
     * @param string $message
     * @return self
     */
    public function successMessage(string $message): MjsaResponse;

    /**
     * Show error message
     * @param string $message
     * @return self
     */
    public function errorMessage(string $message): MjsaResponse;

    /**
     * Indication fields with errors in form
     * @param string $class
     * @return self
     */
    public function incorrect(string $class): MjsaResponse;

    /**
     * Replace form content in form
     * @param string $content
     * @return self
     */
    public function formReplace(string $content): MjsaResponse;

    /**
     * Reload page
     * @return self
     */
    public function reload(): MjsaResponse;

    /**
     * Close all popups
     * @return self
     */
    public function closePopups(): MjsaResponse;

    /**
     * @param string $content
     * @param string $name
     * @param array $options
     * <br/> maxWidth => int
     * <br/> close => bool
     * @return self
     */
    public function openPopupWithData(string $content, string $name, array $options): MjsaResponse;

    /**
     * TODO move out to validator service
     * @param array $input Input array like $_POST
     * @param array $config Array with config input data <pre>
     *   [
     *   'filters' => [
     *      'trim' => true,
     *      'html_chars' => true,
     *      'phone' => ['8029' => +37529, '80' => '+375'],
     *   ],
     *   'required' => true,
     *   'required_text' => 'This field is required',
     *   'allow_empty' => true,
     *   'max_length' => 255,
     *   'max_length_text' => 'This field with maximum 255 chars',
     *   'length' => 8,
     *   'length_text' => 'This field need with with length is 8 chars',
     *   'email' => true,
     *   'email_text' => 'This field will need to be email',
     *   'phone_length' => strlen('+37529xxxyyzz'),
     *   'phone_length_text' => 'This field neet to me valid phone',
     * ]
     * </pre>
     * @return array|null
     */
    public function validator(array $input, array $config): ?array;
}
