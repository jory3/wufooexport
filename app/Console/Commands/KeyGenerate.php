<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class KeyGenerate extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'key:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets the application key';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $key = $this->getRandomKey();
        if ($this->option('show')) {
            return $this->line('<comment>' . $key . '</comment>');
        }

        $path = base_path('.env');
        if (!\file_exists($path)) {
            exit($this->error('.env file does not exist!'));
        }

        \file_put_contents(
            $path,
            \str_replace(
                'APP_KEY=' . env('APP_KEY'),
                'APP_KEY=' . $key,
                \file_get_contents($path)
            )
        );

        $this->info('Application key set successfully.');
    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     */
    protected function getRandomKey()
    {
        return \base64_encode(\random_bytes(32));
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'show',
                null,
                InputOption::VALUE_NONE,
                'Simply display the key instead of modifying files.',
            ],
        ];
    }
}
