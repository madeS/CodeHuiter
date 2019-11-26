<?php

namespace CodeHuiter\Pattern\Service\ByDefault;

use CodeHuiter\Core\Request;
use CodeHuiter\Core\Response;
use CodeHuiter\Modifier\StringModifier;
use CodeHuiter\Pattern\Service\AjaxResponse;
use CodeHuiter\Service\Language;

class MjsaAjaxResponse implements AjaxResponse
{
    /**
     * @var string
     */
    protected $eventResponse = '';

    /**
     * @var string
     */
    protected $mjsaMark = '<mjsa_separator/>';

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
            && ($request->getRequestValue('mjsaAjax') || $request->getRequestValue('bodyAjax'));
    }

    /**
     * {@inheritDoc}
     */
    public function isBodyAjax(Request $request): bool
    {
        return $request->isAJAX() && $request->getRequestValue('bodyAjax');
    }


    /**
     * {@inheritDoc}
     */
    public function events(): AjaxResponse
    {
        $this->eventResponse = '';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function render(?Response $response = null): string
    {
        $result = $this->eventResponse;
        $this->eventResponse = '';
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
        $this->eventResponse .= $this->mjsaMark . '<stop_separator/><location_separator/>'.$url.'<location_separator/>';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function insert(string $selector, string $content): AjaxResponse
    {
        $this->eventResponse .= $this->mjsaMark . '<noservice_separator/>'
            . '<html_replace_separator/>'.$selector.'<html_replace_to/>'
            . $content
            . '<html_replace_separator/>'
            . '<noservice_separator/>';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function append(string $selector, string $content): AjaxResponse
    {
        $this->eventResponse .= $this->mjsaMark . '<noservice_separator/>'
            .'<html_append_separator/>'.$selector.'<html_append_to/>'
            . $content
            .'<html_append_separator/>'
            .'<noservice_separator/>';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function successMessage(string $message): AjaxResponse
    {
        $this->eventResponse .= $this->mjsaMark . '<success_separator/>' . $message . '<success_separator/>';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function errorMessage(string $message): AjaxResponse
    {
        $this->eventResponse .= $this->mjsaMark . '<error_separator/>' . $message . '<error_separator/>';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function incorrect(string $field): AjaxResponse
    {
        $this->eventResponse .= $this->mjsaMark . '<incorrect_separator/>' . $field . '<incorrect_separator/>';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function formReplace(string $content): AjaxResponse
    {
        $this->eventResponse .= $this->mjsaMark . '<form_replace_separator/>' . $content . '<form_replace_separator/>';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function reload(): AjaxResponse
    {
        $this->eventResponse .= $this->mjsaMark . '<script>mjsa.bodyUpdate();</script>';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function closePopups(): AjaxResponse
    {
        $this->eventResponse .= $this->mjsaMark . '<script>mjsa.popups.closeAll();</script>';
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
        $this->eventResponse .= $this->mjsaMark
            . '<open_content_popup_separator/>'
            . ($name ? $name : 'defaultPopup')
            . '<open_content_data/>'
            . $content
            . '<open_content_data/>'
            . ($options ? StringModifier::jsonEncode($options) : '{}')
            . '<open_content_popup_separator/>';
        return $this;
    }

    public function clearEvents(): void
    {
        $this->events();
    }

    public function hasEvents(): bool
    {
        return $this->eventResponse ? true : false;
    }
}
