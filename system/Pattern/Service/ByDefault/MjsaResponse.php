<?php

namespace CodeHuiter\Pattern\Service\ByDefault;

use CodeHuiter\Core\Request;
use CodeHuiter\Core\Response;
use CodeHuiter\Modifier\Filter;
use CodeHuiter\Modifier\StringModifier;
use CodeHuiter\Modifier\Validator;
use CodeHuiter\Pattern\Service\MjsaResponse as MjsaResponseInterface;
use CodeHuiter\Service\Language;

class MjsaResponse implements MjsaResponseInterface
{
    /**
     * @var string
     */
    protected $eventResponse = '';

    /**
     * @var string
     */
    protected $response = '';

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
    public function isMjsaRequested(Request $request): bool
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
    public function events(): MjsaResponseInterface
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
        $this->eventResponse = null;
        if ($response !== null) {
            $response->append($result);
        }
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function location(string $url): MjsaResponseInterface
    {
        $this->eventResponse .= $this->mjsaMark . '<stop_separator/><location_separator/>'.$url.'<location_separator/>';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function insert(string $selector, string $content): MjsaResponseInterface
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
    public function append(string $selector, string $content): MjsaResponseInterface
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
    public function successMessage(string $message): MjsaResponseInterface
    {
        $this->eventResponse .= $this->mjsaMark . '<success_separator/>' . $message . '<success_separator/>';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function errorMessage(string $message): MjsaResponseInterface
    {
        $this->eventResponse .= $this->mjsaMark . '<error_separator/>' . $message . '<error_separator/>';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function incorrect(string $class): MjsaResponseInterface
    {
        $this->eventResponse .= $this->mjsaMark . '<incorrect_separator/>' . $class . '<incorrect_separator/>';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function formReplace(string $content): MjsaResponseInterface
    {
        $this->eventResponse .= $this->mjsaMark . '<form_replace_separator/>' . $content . '<form_replace_separator/>';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function reload(): MjsaResponseInterface
    {
        $this->eventResponse .= $this->mjsaMark . '<script>mjsa.bodyUpdate();</script>';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function closePopups(): MjsaResponseInterface
    {
        $this->eventResponse .= $this->mjsaMark . '<script>mjsa.popups.closeAll();</script>';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function openPopupWithData(string $content, string $name, array $options): MjsaResponseInterface
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




    /**
     * TODO implement render on response where it used
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
                $this->errorMessage(($options['required_text'] ?? $this->language->get('mjsa_validator:required')));
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
                    ?? $this->language->get('mjsa_validator:max_length',['{#max_length}' => $options['max_length']])
                );
                $this->incorrect($field);
                if ($focusOneError) break;
                else continue;
            }
            if (($options['length'] ?? null) && strlen($data) !== $options['length']) {
                $this->errorMessage(
                    $options['length_text']
                    ?? $this->language->get('mjsa_validator:length',['{#length}' => $options['length']])
                );
                $this->incorrect($field);
                if ($focusOneError) break;
                else continue;
            }
            if (($options['email'] ?? null) && !Validator::isValidEmail($data)) {
                $this->errorMessage(
                    $options['email_text']
                    ?? $this->language->get('mjsa_validator:email')
                );
                $this->incorrect($field);
                if ($focusOneError) break;
                else continue;
            }
            if (($options['phone_length'] ?? null) && strlen($data) !== $options['phone_length']) {
                $this->errorMessage(
                    $options['phone_length_text']
                    ?? $this->language->get(
                        'mjsa_validator:phone_length',
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
        if ($this->eventResponse) {
            return null;
        }
        return $result;
    }
}
