<?php

namespace CodeHuiter\Pattern\Service\ByDefault;

use CodeHuiter\Modifier\Filter;
use CodeHuiter\Pattern\Service\AjaxResponse;
use CodeHuiter\Pattern\Service\ValidatedData;
use CodeHuiter\Service\Language;

class Validator implements \CodeHuiter\Pattern\Service\Validator
{
    /**
     * @var Language
     */
    protected $language;

    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    public function validate(array $input, array $config, AjaxResponse $ajaxResponse): ?ValidatedData
    {
        $focusOneError = true;

        $ajaxResponse->clearEvents();
        $result = new \CodeHuiter\Pattern\Service\ByDefault\ValidatedData();

        foreach ($config as $field => $options) {
            $fieldExist = isset($input[$field]);
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
                $ajaxResponse->errorMessage($options['required_text'] ?? $this->language->get('ajax_validator:required'));
                $ajaxResponse->incorrect($field);
                if ($focusOneError) break;
                else continue;
            }
            if (($options['allow_empty'] ?? null) && $data === '') {
                $result->setField($field, $data, $fieldExist);
                continue;
            }
            if (($options['max_length'] ?? null) && strlen($data) > $options['max_length']) {
                $ajaxResponse->errorMessage(
                    $options['max_length_text']
                    ?? $this->language->get('ajax_validator:max_length',['{#max_length}' => $options['max_length']])
                );
                $ajaxResponse->incorrect($field);
                if ($focusOneError) break;
                else continue;
            }
            if (($options['length'] ?? null) && strlen($data) !== $options['length']) {
                $ajaxResponse->errorMessage(
                    $options['length_text']
                    ?? $this->language->get('ajax_validator:length',['{#length}' => $options['length']])
                );
                $ajaxResponse->incorrect($field);
                if ($focusOneError) break;
                else continue;
            }
            if (($options['email'] ?? null) && !\CodeHuiter\Modifier\Validator::isValidEmail($data)) {
                $ajaxResponse->errorMessage(
                    $options['email_text']
                    ?? $this->language->get('ajax_validator:email')
                );
                $ajaxResponse->incorrect($field);
                if ($focusOneError) break;
                else continue;
            }
            if (($options['phone_length'] ?? null) && strlen($data) !== $options['phone_length']) {
                $ajaxResponse->errorMessage(
                    $options['phone_length_text']
                    ?? $this->language->get(
                        'ajax_validator:phone_length',
                        ['{#phone_length}' => $options['phone_length']]
                    )
                );
                $ajaxResponse->incorrect($field);
                if ($focusOneError) break;
                else continue;
            }
            if (($options['callback'] ?? null) && is_callable($options['callback'])) {
                $callback = $options['callback'];
                $callbackResult = $callback($data);
                if ($callback($data) !== true) {
                    $ajaxResponse->errorMessage($callbackResult);
                    $ajaxResponse->incorrect($field);
                    if ($focusOneError) break;
                    else continue;
                }
            }
            $result->setField($field, $data, $fieldExist);
        }
        if ($ajaxResponse->hasEvents()) {
            return null;
        }
        return $result;
    }
}
