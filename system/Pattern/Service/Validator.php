<?php

namespace CodeHuiter\Pattern\Service;

use CodeHuiter\Core\Request;
use CodeHuiter\Core\Response;

interface Validator
{
    public const VALIDATOR_SETTLED_FIELDS = 'settled';

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
     * @param AjaxResponse $ajaxResponse
     * @return ValidatedData|null
     */
    public function validate(array $input, array $config, AjaxResponse $ajaxResponse): ?ValidatedData;
}
