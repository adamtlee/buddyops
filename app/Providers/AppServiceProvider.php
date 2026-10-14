<?php

namespace App\Providers;

use App\Services\Llm\AnthropicClient;
use App\Services\Llm\FakeLlmClient;
use App\Services\Llm\LlmClient;
use LogicException;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LlmClient::class, function (): LlmClient {
            $driver = config('services.llm.driver');

            if ($driver === 'fake') {
                if (app()->environment('production')) {
                    throw new LogicException('The fake LLM driver cannot be used in production. Set LLM_DRIVER=anthropic.');
                }

                return new FakeLlmClient();
            }

            if ($driver === 'anthropic') {
                return new AnthropicClient();
            }

            throw new LogicException("Unsupported LLM driver [{$driver}]. Set LLM_DRIVER to fake or anthropic.");
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
