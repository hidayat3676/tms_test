<?php

namespace Tests\Unit;

use App\Contracts\TranslationRepositoryInterface;
use App\Services\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;
use Mockery;

class TranslationServiceTest extends TestCase
{
    use RefreshDatabase;
    protected $repositoryMock;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = Mockery::mock(TranslationRepositoryInterface::class);
        $this->service = new TranslationService($this->repositoryMock);
    }

    public function test_create_translation()
    {
        $data = ['key' => 'welcome', 'locale' => 'en', 'value' => 'Welcome'];

        $this->repositoryMock->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn('created');

        $result = $this->service->createTranslation($data);
        $this->assertEquals('created', $result);
    }

    public function test_update_translation()
    {
        $data = ['key' => 'welcome', 'value' => 'Welcome Again'];

        $this->repositoryMock->shouldReceive('update')
            ->once()
            ->with(1, $data)
            ->andReturn(true);

        $this->assertTrue($this->service->updateTranslation(1, $data));
    }

    public function test_search_translations()
    {
        $filters = ['key' => 'welcome'];

        $this->repositoryMock->shouldReceive('search')
            ->once()
            ->with($filters)
            ->andReturn(['result']);

        $this->assertEquals(['result'], $this->service->searchTranslations($filters));
    }

    public function test_delete_translation()
    {
        $this->repositoryMock->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $this->assertTrue($this->service->deleteTranslation(1));
    }

    public function test_find_translation()
    {
        $this->repositoryMock->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn(['id' => 1]);

        $this->assertEquals(['id' => 1], $this->service->findTranslation(1));
    }

    public function test_all_translations()
    {
        $this->repositoryMock->shouldReceive('all')
            ->once()
            ->andReturn(['t1', 't2']);

        $this->assertEquals(['t1', 't2'], $this->service->allTranslations());
    }

    public function test_get_by_locale()
    {
        $this->repositoryMock->shouldReceive('getByLocale')
            ->once()
            ->with('en')
            ->andReturn(['t1']);

        $this->assertEquals(['t1'], $this->service->getByLocale('en'));
    }

    public function test_find_by_key_and_locale()
    {
        $this->repositoryMock->shouldReceive('findByKeyAndLocale')
            ->once()
            ->with('welcome', 'en')
            ->andReturn(['id' => 1]);

        $this->assertEquals(['id' => 1], $this->service->findByKeyAndLocale('welcome', 'en'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
