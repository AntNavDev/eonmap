<?php

declare(strict_types=1);

namespace Tests\Unit\Api;

use App\Api\AbstractApiConnection;
use App\Api\Exceptions\ApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AbstractApiConnectionTest extends TestCase
{
    /**
     * Instantiate a concrete subclass of the abstract connection using the
     * supplied base URL, without requiring any config or constructor overrides.
     */
    private function makeConnection(string $baseUrl = 'https://test.example.com'): AbstractApiConnection
    {
        return new class($baseUrl) extends AbstractApiConnection {};
    }

    // ---------------------------------------------------------------------------
    // .json extension handling
    // ---------------------------------------------------------------------------

    public function test_get_appends_json_extension_when_absent(): void
    {
        Http::fake(['https://test.example.com/occs/list.json' => Http::response(['records' => []])]);

        $this->makeConnection()->get('/occs/list');

        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '.json'));
    }

    public function test_get_does_not_duplicate_json_extension(): void
    {
        Http::fake(['https://test.example.com/occs/list.json' => Http::response(['records' => []])]);

        $this->makeConnection()->get('/occs/list.json');

        Http::assertSentCount(1);
        Http::assertSent(fn (Request $r) => ! str_contains($r->url(), '.json.json'));
    }

    // ---------------------------------------------------------------------------
    // User-Agent header
    // ---------------------------------------------------------------------------

    /**
     * PBDB returns 403 to requests without a proper User-Agent from Docker
     * containers. The header must be present on every request.
     */
    public function test_get_sends_user_agent_header(): void
    {
        Http::fake(['*' => Http::response(['records' => []])]);

        $this->makeConnection()->get('/occs/list');

        Http::assertSent(fn (Request $r) => $r->hasHeader('User-Agent'));
    }

    public function test_user_agent_header_identifies_the_application(): void
    {
        Http::fake(['*' => Http::response(['records' => []])]);

        $this->makeConnection()->get('/occs/list');

        Http::assertSent(fn (Request $r) => str_contains($r->header('User-Agent')[0], 'Eonmap'));
    }

    // ---------------------------------------------------------------------------
    // Successful response
    // ---------------------------------------------------------------------------

    public function test_get_returns_decoded_json_body_on_success(): void
    {
        Http::fake(['*' => Http::response(['records' => [['oid' => 'occ:1']]])]);

        $result = $this->makeConnection()->get('/occs/list');

        $this->assertSame([['oid' => 'occ:1']], $result['records']);
    }

    public function test_get_passes_query_params_to_request(): void
    {
        Http::fake(['*' => Http::response(['records' => []])]);

        $this->makeConnection()->get('/occs/list', ['base_name' => 'Dinosauria', 'limit' => 500]);

        Http::assertSent(function (Request $r) {
            $url = urldecode($r->url());

            return str_contains($url, 'base_name=Dinosauria')
                && str_contains($url, 'limit=500');
        });
    }

    // ---------------------------------------------------------------------------
    // Error handling
    // ---------------------------------------------------------------------------

    public function test_get_throws_api_exception_on_non_2xx_response(): void
    {
        Http::fake(['*' => Http::response('Forbidden', 403)]);

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        $this->makeConnection()->get('/occs/list');
    }

    public function test_api_exception_message_includes_status_code_on_non_2xx(): void
    {
        Http::fake(['*' => Http::response('Internal Server Error', 500)]);

        try {
            $this->makeConnection()->get('/occs/list');
            $this->fail('Expected ApiException was not thrown');
        } catch (ApiException $e) {
            $this->assertStringContainsString('500', $e->getMessage());
        }
    }

    public function test_get_throws_api_exception_on_connection_failure(): void
    {
        Http::fake(['*' => function () {
            throw new ConnectionException('Could not resolve host');
        }]);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Connection failed');

        $this->makeConnection()->get('/occs/list');
    }

    public function test_connection_exception_is_wrapped_as_previous_exception(): void
    {
        $original = new ConnectionException('Timeout');

        Http::fake(['*' => function () use ($original) {
            throw $original;
        }]);

        try {
            $this->makeConnection()->get('/occs/list');
            $this->fail('Expected ApiException was not thrown');
        } catch (ApiException $e) {
            $this->assertSame($original, $e->getPrevious());
        }
    }
}
