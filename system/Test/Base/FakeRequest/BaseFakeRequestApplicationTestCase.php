<?php

namespace CodeHuiter\Test\Base\FakeRequest;

use CodeHuiter\Core\Application;
use CodeHuiter\Core\Response;
use CodeHuiter\Modifier\StringModifier;
use CodeHuiter\Service\HtmlParser;
use CodeHuiter\Service\Language;
use PHPUnit\Framework\TestCase;

class BaseFakeRequestApplicationTestCase extends TestCase
{
    protected static function getApplicationTestExecutor(): ApplicationTestExecutor
    {
        return ApplicationTestExecutor::getInstance();
    }

    protected static function getApplication(): Application
    {
        return self::getApplicationTestExecutor()->getApplication();
    }

    protected static function runWithGet(BaseFakeRequestApplicationTestCase $those, string $uri, array $cookie = []): Response
    {
        $applicationTestExecutor = self::getApplicationTestExecutor();
        $response = $applicationTestExecutor->runWithGetRequest($uri, $cookie);
        if ($those->getActualOutput()) {
            self::fail("While run with GET URI [$uri] got output [{$those->getActualOutput()}]");
        }
        if ($response === null) {
            self::fail("Response by URI [$uri] is null");
        }
        return $response;
    }

    protected static function runWithPost(BaseFakeRequestApplicationTestCase $those, string $uri, array $data, array $cookie = []): Response
    {
        $applicationTestExecutor = self::getApplicationTestExecutor();
        $response = $applicationTestExecutor->runWithPostRequest($uri, $data, $cookie);
        if ($those->getActualOutput()) {
            self::fail("While run with GET URI [$uri] got output [{$those->getActualOutput()}]");
        }
        if ($response === null) {
            self::fail("Response by URI $uri is null");
        }
        return $response;
    }


    protected static function jsonDecode(string $jsonContent): array
    {
        $json = StringModifier::jsonDecode($jsonContent, false);
        if ($json === null) {
            self::fail('runWithJsonAjaxRequest return not json value: ' . $jsonContent);
        }
        return $json;
    }

    protected static function getJsonAjaxFormReplaceContent(Response $response): ?string
    {
        $json = self::jsonDecode($response->getContent());
        if (isset($json['events']) && is_array($json['events'])) {
            foreach ($json['events'] as $key => $event) {
                if ($event['type'] === 'ajax_form_replace') {
                    return $event['data']['content'] ?? null;
                }
            }
        }
        return null;
    }

    protected static function getJsonAjaxSuccessMessage(Response $response): ?string
    {
        $json = self::jsonDecode($response->getContent());
        if (isset($json['events']) && is_array($json['events'])) {
            foreach ($json['events'] as $key => $event) {
                if ($event['type'] === 'success_message') {
                    return $event['data']['message'] ?? null;
                }
            }
        }
        return null;
    }

    protected static function getJsonAjaxErrorMessage(Response $response): ?string
    {
        $json = self::jsonDecode($response->getContent());
        if (isset($json['events']) && is_array($json['events'])) {
            foreach ($json['events'] as $key => $event) {
                if ($event['type'] === 'error_message') {
                    return $event['data']['message'] ?? null;
                }
            }
        }
        return null;
    }

    protected static function getJsonAjaxIncorrectInputs(Response $response): array
    {
        $json = self::jsonDecode($response->getContent());
        $result = [];
        if (isset($json['events']) && is_array($json['events'])) {
            foreach ($json['events'] as $key => $event) {
                if (is_int($key) && $event['type'] === 'ajax_form_incorrect_input') {
                    $result[] = $event['data']['field'] ?? null;
                }
            }
        }
        return $result;
    }

    protected static function getHeaderLocation(Response $response): ?string
    {
        $headers = $response->getHeaders();
        foreach ($headers as $header) {
            $headerExploded = explode(':', $header[0]);
            if (trim($headerExploded[0]) === 'Location') {
                return trim($headerExploded[1]);
            }
        }
        return null;
    }

    protected static function getHeaderCode(Response $response): int
    {
        $headers = $response->getHeaders();
        foreach ($headers as $header) {
            if ($header[2] !== null) {
                return $header[2];
            }
        }
        return 200;
    }

    protected static function getCookies(Response $response): array
    {
        $result = [];
        $cookies = $response->getCookies();
        foreach ($cookies as $cookie) {
            $result[$cookie[0]] = $cookie[1];
        }
        return $result;
    }

    protected static function assertHasIncorrectInputs(string $input, Response $response): void
    {
        $incorrectFields = self::getJsonAjaxIncorrectInputs($response);
        if (!in_array($input, $incorrectFields, true)) {
            self::fail(sprintf(
                'Response has not contain expected incorrect field. Expected: [%s] Got: [%s]',
                $input,
                implode(',', $incorrectFields)
            ));
        } else {
            self::assertTrue(true);
        }
    }

    protected static function assertHasErrorMessage($message, Response $response): void
    {
        $responseErrorMessage = self::getJsonAjaxErrorMessage($response);
        self::assertEquals(
            $message,
            $responseErrorMessage,
            sprintf(
                'Response has not contain expected error message. Expected: [%s], has: [%s]',
                $message,
                $responseErrorMessage
            )
        );
    }

    protected static function assertHasSuccessMessage($message, Response $response): void
    {
        $responseSuccessMessage = self::getJsonAjaxSuccessMessage($response);
        self::assertEquals(
            $message,
            $responseSuccessMessage,
            sprintf(
                'Response has not contain expected success message. Expected: [%s], has: [%s]',
                $message,
                $responseSuccessMessage
            )
        );
    }

    protected static function assertResponseWithoutError(Response $response): void
    {
        self::assertEquals(
            200,
            self::getHeaderCode($response),
            'Response has invalid status code. Expected: 200, Got: ' . self::getHeaderCode($response)
        );
        self::assertStringNotContainsString(
            '[!APP-FAILED!]',
            $response->getContent(),
            'Response contain an error on page'
        );
    }

    protected static function getParser(): HtmlParser
    {
        return self::getApplication()->get(HtmlParser::class);
    }

    protected static function getLang(): Language
    {
        return self::getApplication()->get(Language::class);
    }
}

