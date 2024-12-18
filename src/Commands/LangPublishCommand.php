<?php

namespace Laravel\Pulse\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * @internal
 */
#[AsCommand(name: 'pulse:lang-publish')]
class LangPublishCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pulse:lang-publish
                    {--existing : Publish and overwrite only the files that have already been published}
                    {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all language files that are available for customization';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $stubs = [];
        $filename = 'pulse.php';
        $languages = ['lang/en', 'lang/zh_CN', 'lang/zh_TW'];

        foreach ($languages as $language) {
            if (!is_dir($langPath = $this->laravel->basePath($language))) {
                (new Filesystem)->makeDirectory($langPath, recursive: true);
            }

            $key = realpath(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . $filename);
            $stubs[$key] = $this->laravel->basePath($language);
        }

        foreach ($stubs as $from => $to) {
            $to = $to . DIRECTORY_SEPARATOR . $filename;

            if ((!$this->option('existing') && (!file_exists($to) || $this->option('force')))
                || ($this->option('existing') && file_exists($to))) {
                file_put_contents($to, file_get_contents($from));
            }
        }

        $this->components->info('Language files published successfully.');
    }

}
