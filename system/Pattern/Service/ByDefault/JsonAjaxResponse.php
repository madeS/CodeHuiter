<?php

namespace CodeHuiter\Pattern\Service\ByDefault;

use CodeHuiter\Core\Request;
use CodeHuiter\Core\Response;
use CodeHuiter\Modifier\StringModifier;
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

    public function clearEvents(): void
    {
        $this->events();
    }

    public function hasEvents(): bool
    {
        return $this->eventResponse['events'] ? true : false;
    }
}
