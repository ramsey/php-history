<?php

declare(strict_types=1);

namespace App\Commands;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\Validator;
use SplDoublyLinkedList;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Yaml\Yaml;
use function Termwind\render;

class ValidateCommand extends Command
{
    private const string DATA_DIR = __DIR__.'/../../data';

    private const string DEFAULT_TYPE = 'event';

    private const string EVENT_SCHEMA_FILE = __DIR__.'/../../schema/event.json';

    private const string EVENT_SCHEMA_ID = 'https://phpc.dev/schema/php-history/event.json';

    private const array TYPE_SCHEMA_MAP = [
        'event' => self::EVENT_SCHEMA_ID,
    ];

    private const array VALID_TYPES = [
        'event',
    ];

    /**
     * @var string
     */
    protected $signature = 'validate
                            {file?* : The data file(s) to validate (optional)}';

    /**
     * @var string
     */
    protected $description = 'Validate data files';

    /**
     * @var string
     */
    protected $help = <<<'EOD'
        Unless passing one or more files as arguments, this validates all data files in
        the data directory.
        EOD;

    private string $workingDirectory;

    public function __construct(
        private readonly Validator $validator,
        private readonly ErrorFormatter $formatter,
    ) {
        parent::__construct();

        $this->workingDirectory = getcwd();

        $this->validator->resolver()->registerFile(self::EVENT_SCHEMA_ID, self::EVENT_SCHEMA_FILE);
        $this->validator->setMaxErrors(10);
    }

    public function handle(): int
    {
        $this->renderWelcome();

        $list = new SplDoublyLinkedList;
        $files = $this->argument('file');

        if ($files === []) {
            $files[] = self::DATA_DIR;
        }

        foreach ($files as $file) {
            $list->push($file);
        }

        $success = true;

        foreach ($list as $file) {
            if (File::isDirectory($file)) {
                $this->addFilesFromDirectory($file, $list);
            } else {
                $success = $this->validateFile($file) && $success;
            }
        }

        return $success ? 0 : 1;
    }

    private function addFilesFromDirectory(string $directory, SplDoublyLinkedList $list): void
    {
        foreach (File::allFiles($directory) as $file) {
            if ($file->getExtension() === 'yml' || $file->getExtension() === 'yaml') {
                $list->push(Path::makeRelative($file->getRealPath(), $this->workingDirectory));
            }
        }
    }

    private function validateFile(string $file): bool
    {
        $relativePath = Path::isAbsolute($file) ? Path::makeRelative($file, $this->workingDirectory) : $file;

        if (! File::exists($file)) {
            return ! $this->renderNotFound($relativePath);
        }

        if (File::extension($file) !== 'yml' && File::extension($file) !== 'yaml') {
            return ! $this->renderNotYaml($relativePath);
        }

        // Deeply convert the data to a stdClass object, as expected by the validator.
        $data = json_decode(json_encode(Yaml::parseFile($file)));

        $type = in_array($data->type ?? null, self::VALID_TYPES) ? $data->type : self::DEFAULT_TYPE;

        $result = $this->validator->validate($data, self::TYPE_SCHEMA_MAP[$type]);

        if ($result->isValid()) {
            return $this->renderValid($relativePath);
        }

        return $this->renderInvalid($relativePath, $result);
    }

    private function renderWelcome(): void
    {
        render(<<<'HTML'
            <div class="py-1 ml-2">
                <span class="px-1 bg-purple-900 text-gray-50">PHP History</span>
                <span class="ml-1"><em>Validating data files...</em></span>
            </div>
        HTML);
    }

    private function renderNotFound(string $file): false
    {
        render(<<<HTML
            <div class="ml-2">
                <span class="w-13 bg-yellow-600 text-black text-center italic">not found</span>
                <span class="ml-1 text-yellow-300">{$file}</span>
            </div>
        HTML);

        return false;
    }

    private function renderNotYaml(string $file): false
    {
        render(<<<HTML
            <div class="ml-2">
                <span class="w-13 bg-yellow-600 text-black text-center italic">not yaml</span>
                <span class="ml-1 text-yellow-300">{$file}</span>
            </div>
        HTML);

        return false;
    }

    private function renderValid(string $file): true
    {
        render(<<<HTML
            <div class="ml-2">
                <span class="w-13 bg-green-600 text-gray-50 text-center italic">✔︎ valid</span>
                <span class="ml-1 text-green-500">{$file}</span>
            </div>
        HTML);

        return true;
    }

    private function renderInvalid(string $file, ValidationResult $result): false
    {
        render(<<<HTML
            <div class="ml-2">
                <span class="w-13 bg-red-600 text-gray-50 text-center italic">✖︎ invalid</span>
                <span class="ml-1 text-red-500">{$file}</span>
            </div>
        HTML);

        $formattedErrors = $this->formatter->format($result->error());

        $html = '<div class="ml-16 pb-1">';

        foreach ($formattedErrors as $path => $errors) {
            foreach ($errors as $error) {
                $html .= <<<HTML
                    <div>
                        <span class="text-cyan-500 italic">{$path}</span>: {$error}
                    </div>
                    HTML;
            }
        }

        $html .= '</div>';
        render($html);

        return false;
    }
}
