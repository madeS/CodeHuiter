<?php

namespace CodeHuiter\Pattern\Service\ByDefault;

use CodeHuiter\Core\Request;
use CodeHuiter\Core\Response;
use CodeHuiter\Modifier\Filter;
use CodeHuiter\Modifier\StringModifier;
use CodeHuiter\Modifier\Validator;
use CodeHuiter\Pattern\Service\AjaxResponse;
use CodeHuiter\Service\Language;

class JsonAjaxResponse implements AjaxResponse
{
    /**
     * @var array
     */
    protected $eventResponse = [];

    /**
     * @var Language
     */
    protected $language;

    /**
     * @param Language $language
     */
    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    /**
     * {@inheritDoc}
     */
    public function isAjaxRequested(Request $request): bool
    {
        return $request->isAJAX()
            && ($request->getRequestValue('jsonAjax') || $request->getRequestValue('bodyJsonAjax'));
    }

    /**
     * {@inheritDoc}
     */
    public function isBodyAjax(Request $request): bool
    {
        return $request->isAJAX() && $request->getRequestValue('bodyJsonAjax');
    }


    /**
     * {@inheritDoc}
     */
    public function events(): AjaxResponse
    {
        $this->eventResponse = ['events' => []];
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function render(?Response $response = null): string
    {
        $resultData = $this->eventResponse;
        $this->eventResponse = ['events' => []];
        $result = StringModifier::jsonEncode($resultData);
        if ($response !== null) {
            $response->append($result);
        }
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function location(string $url): AjaxResponse
    {
        $this->eventResponse['events'][] = [
            'type' => 'location',
            'data' => [
                'url' => $url,
            ]
        ];
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function insert(string $selector, string $content): AjaxResponse
    {
        $this->eventResponse['events'][] = [
            'type' => 'html_replace',
            'data' => [
                'selector' => $selector,
                'content' => $content,
            ]
        ];
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function append(string $selector, string $content): AjaxResponse
    {
        $this->eventResponse['events'][] = [
            'type' => 'html_append',
            'data' => [
                'selector' => $selector,
                'content' => $content,
            ]
        ];
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function successMessage(string $message): AjaxResponse
    {
        $this->eventResponse['events'][] = [
            'type' => 'success_message',
            'data' => [
                'message' => $message,
            ]
        ];
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function errorMessage(string $message): AjaxResponse
    {
        $this->eventResponse['events'][] = [
            'type' => 'error_message',
            'data' => [
                'message' => $message,
            ]
        ];
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function incorrect(string $field): AjaxResponse
    {
        $this->eventResponse['events'][] = [
            'type' => 'ajax_form_incorrect_input',
            'data' => [
                'field' => $field,
            ]
        ];
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function formReplace(string $content): AjaxResponse
    {
        $this->eventResponse['events'][] = [
            'type' => 'ajax_form_replace',
            'data' => [
                'content' => $content,
            ]
        ];
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function reload(): AjaxResponse
    {
        $this->eventResponse['events'][] = [
            'type' => 'reload',
            'data' => [
            ]
        ];
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function closePopups(): AjaxResponse
    {
        $this->eventResponse['events'][] = [
            'type' => 'popup_close_all',
            'data' => [
            ]
        ];
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function openPopupWithData(string $content, string $name, array $options): AjaxResponse
    {
        if (isset($options['close']) && $options['close']) {
            unset($options['close']);
            $options['closeBtnClass'] = 'ficon-cancel';
        }
        $this->eventResponse['events'][] = [
            'type' => 'popup_open_with_content',
            'data' => [
                'name' => $name ?: 'defaultPopup',
                'content' => $content,
                'options' => $options
            ]
        ];
        return $this;
    }

    /**
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
    public function validator(array $input, array $config): ?array
    {
        $focusOneError = true;

        $this->events();
        $result = [];
        foreach ($config as $field => $options) {
            $data = $input[$field] ?? '';

            $filters = $options['filters'] ?? [];
            if ($filters) {
                foreach ($filters as $filter => $filterParams) {
                    if ($filter === 'trim') {
                        $data = trim($data);
                    }
                    if ($filter === 'html_chars') {
                        $data = trim($data);
                    }
                    if ($filter === 'phone') {
                        $data = Filter::phoneClearHard($data, $filterParams);
                    }
                }
            }

            if (($options['required'] ?? null) && $data === '') {
                $this->errorMessage(($options['required_text'] ?? $this->language->get('ajax_validator:required')));
                $this->incorrect($field);
                if ($focusOneError) break;
                else continue;
            }
            if (($options['allow_empty'] ?? null) && $data === '') {
                $result[$field] = $data;
                continue;
            }
            if (($options['max_length'] ?? null) && strlen($data) > $options['max_length']) {
                $this->errorMessage(
                    $options['max_length_text']
                    ?? $this->language->get('ajax_validator:max_length',['{#max_length}' => $options['max_length']])
                );
                $this->incorrect($field);
                if ($focusOneError) break;
                else continue;
            }
            if (($options['length'] ?? null) && strlen($data) !== $options['length']) {
                $this->errorMessage(
                    $options['length_text']
                    ?? $this->language->get('ajax_validator:length',['{#length}' => $options['length']])
                );
                $this->incorrect($field);
                if ($focusOneError) break;
                else continue;
            }
            if (($options['email'] ?? null) && !Validator::isValidEmail($data)) {
                $this->errorMessage(
                    $options['email_text']
                    ?? $this->language->get('ajax_validator:email')
                );
                $this->incorrect($field);
                if ($focusOneError) break;
                else continue;
            }
            if (($options['phone_length'] ?? null) && strlen($data) !== $options['phone_length']) {
                $this->errorMessage(
                    $options['phone_length_text']
                    ?? $this->language->get(
                        'ajax_validator:phone_length',
                        ['{#phone_length}' => $options['phone_length']]
                    )
                );
                $this->incorrect($field);
                if ($focusOneError) break;
                else continue;
            }
            if (($options['callback'] ?? null) && is_callable($options['callback'])) {
                $callback = $options['callback'];
                $callbackResult = $callback($data);
                if ($callback($data) !== true) {
                    $this->errorMessage($callbackResult);
                    $this->incorrect($field);
                    if ($focusOneError) break;
                    else continue;
                }
            }
            $result[$field] = $data;
        }
        if ($this->eventResponse['events']) {
            return null;
        }
        return $result;
    }
}
