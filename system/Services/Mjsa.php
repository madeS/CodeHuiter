<?php

namespace CodeHuiter\Services;

use CodeHuiter\Config\Config;
use CodeHuiter\Core\Application;
use CodeHuiter\Modifiers\Filter;
use CodeHuiter\Modifiers\StringModifier;
use CodeHuiter\Modifiers\Validator;

class Mjsa
{
    /**
     * @var string|null
     */
    protected $eventResponse = null;

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
    protected $lang;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->lang = $app->get(Config::SERVICE_KEY_LANG);;
    }

    /**
     * Start events
     * @return self
     */
    public function events()
    {
        $this->eventResponse = '';
        return $this;
    }

    /**
     * End events
     * @param bool $print
     * @return null|string
     */
    public function send($print = true)
    {
        $result = null;
        if ($print) {
            echo $this->eventResponse;
        } else {
            $result = $this->eventResponse;
        }
        $this->eventResponse = null;
        return $result;
    }

    /**
     * Choose action with event
     * @param bool $print
     * @return self|null|string
     */
    protected function actionChoose($print)
    {
        if ($this->eventResponse !== null) {
            $this->eventResponse .= $this->response;
            return $this;
        }
        if (!$print) {
            return $this->response;
        }
        echo $this->response;
        return null;
    }

    /**
     * User redirect
     * @param string $url
     * @param bool $print
     * @return self|null|string
     */
    public function location($url, $print = true)
    {
        $this->response = $this->mjsaMark . '<stop_separator/><location_separator/>'.$url.'<location_separator/>';

        return $this->actionChoose($print);
    }

    /**
     * Insert content into selector block
     * @param string $selector
     * @param string $content
     * @param bool $print
     * @return self|null|string
     */
    public function insert($selector, $content, $print = true)
    {
        $this->response = $this->mjsaMark . '<noservice_separator/>'
            . '<html_replace_separator/>'.$selector.'<html_replace_to/>'
            . $content
            . '<html_replace_separator/>'
            . '<noservice_separator/>';

        return $this->actionChoose($print);
    }

    /**
     * Append content into selector block
     * @param string $selector
     * @param string $content
     * @param bool $print
     * @return self|null|string
     */
    public function append($selector, $content, $print = true)
    {
        $this->response = $this->mjsaMark . '<noservice_separator/>'
            .'<html_append_separator/>'.$selector.'<html_append_to/>'
            . $content
            .'<html_append_separator/>'
            .'<noservice_separator/>';

        return $this->actionChoose($print);
    }

    /**
     * Show success message
     * @param string $message
     * @param bool $print
     * @return self|null|string
     */
    public function successMessage($message, $print = true)
    {
        $this->response = $this->mjsaMark . '<success_separator/>' . $message . '<success_separator/>';

        return $this->actionChoose($print);
    }

    /**
     * Show error message
     * @param string $message
     * @param bool $print
     * @return self|null|string
     */
    public function errorMessage($message, $print = true)
    {
        $this->response = $this->mjsaMark . '<error_separator/>' . $message . '<error_separator/>';

        return $this->actionChoose($print);
    }

    /**
     * Indication fields with errors in form
     * @param string $class
     * @param bool $print
     * @return self|null|string
     */
    public function incorrect($class, $print = true)
    {
        $this->response = $this->mjsaMark . '<incorrect_separator/>' . $class . '<incorrect_separator/>';

        return $this->actionChoose($print);
    }

    /**
     * Replace form content in form
     * @param string $content
     * @param bool $print
     * @return self|null|string
     */
    public function formReplace($content, $print = true)
    {
        $this->response = $this->mjsaMark . '<form_replace_separator/>' . $content . '<form_replace_separator/>';

        return $this->actionChoose($print);
    }



    /**
     * Reload page
     * @param bool $print
     * @return self|null|string
     */
    public function reload($print = true)
    {
        $this->response = $this->mjsaMark . '<script>mjsa.bodyUpdate();</script>';

        return $this->actionChoose($print);
    }

    /**
     * Close all popups
     * @param bool $print
     * @return self|null|string
     */
    public function closePopups($print = true)
    {
        $this->response = $this->mjsaMark . '<script>mjsa.popups.closeAll();</script>';

        return $this->actionChoose($print);
    }

    /**
     * @param string $content
     * @param string $name
     * @param array $options
     * <br/> maxWidth => int
     * <br/> close => bool
     * @param bool $print
     * @return Mjsa|null|string
     */
    public function openPopupWithData($content, $name, $options, $print = true)
    {
        if (isset($options['close']) && $options['close']) {
            unset($options['close']);
            $options['closeBtnClass'] = 'ficon-cancel';
        }
        $this->response = $this->mjsaMark
            . '<open_content_popup_separator/>'
            . ($name ? $name : 'defaultPopup')
            . '<open_content_data/>'
            . $content
            . '<open_content_data/>'
            . ($options ? StringModifier::jsonEncode($options) : '{}')
            . '<open_content_popup_separator/>';

        return $this->actionChoose($print);
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
    public function validator($input, $config)
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
                $this->errorMessage(($options['required_text'] ?? $this->lang->get('mjsa_validator:required')));
                $this->incorrect($field);
                if ($focusOneError) break;
                else continue;
            }
            if (($options['allow_empty'] ?? null) && $data === '') {
                $result[$field] = $data;
                continue;
            }
            if (($options['max_length'] ?? null) && strlen($data) > $options['max_length']) {
                $this->errorMessage((
                    $options['max_length_text']
                    ?? $this->lang->get('mjsa_validator:max_length',['{#max_length}' => $options['max_length']])
                ));
                $this->incorrect($field);
                if ($focusOneError) break;
                else continue;
            }
            if (($options['length'] ?? null) && strlen($data) !== $options['length']) {
                $this->errorMessage((
                    $options['length_text']
                    ?? $this->lang->get('mjsa_validator:length',['{#length}' => $options['length']])
                ));
                $this->incorrect($field);
                if ($focusOneError) break;
                else continue;
            }
            if (($options['email'] ?? null) && !Validator::isValidEmail($data)) {
                $this->errorMessage((
                    $options['email_text']
                    ?? $this->lang->get('mjsa_validator:email')
                ));
                $this->incorrect($field);
                if ($focusOneError) break;
                else continue;
            }
            if (($options['phone_length'] ?? null) && strlen($data) !== $options['phone_length']) {
                $this->errorMessage((
                    $options['phone_length_text']
                    ?? $this->lang->get(
                        'mjsa_validator:phone_length',
                        ['{#phone_length}' => $options['phone_length']]
                    )
                ));
                $this->incorrect($field);
                if ($focusOneError) break;
                else continue;
            }
            $result[$field] = $data;
        }

        $response = $this->send(false);
        if ($response) {
            echo $response;
            return null;
        } else {
            return $result;
        }
    }
}
