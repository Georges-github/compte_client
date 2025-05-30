<?php

namespace App\Outils;

class Tracer
{
    private string $file;

    public function __construct(string $filename = 'trace.txt')
    {
        $this->file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;

        echo( $this->file . "\n" );
    }

    public function log(mixed $value, ?string $label): void
    {
        $timestamp = (new \DateTime())->format('Y-m-d H:i:s');
        $output = "[$timestamp]";

        if ($label) {
            $output .= " $label:";
        }

        $output .= "\n" . $this->stringify($value) . "\n\n";

        file_put_contents($this->file, $output, FILE_APPEND);
    }

    private function stringify(mixed $value): string
    {
        if ($value instanceof \Throwable) {
            return $this->formatException($value);
        }

        if (is_scalar($value) || $value === null) {
            return var_export($value, true);
        }

        if (is_array($value)) {
            return print_r($value, true);
        }

        if (is_object($value)) {
            return print_r($value, true);
        }

        return 'Type non supporté';
    }

    private function formatException(\Throwable $e): string
    {
        return sprintf(
            "Exception de type %s\nMessage : %s\nFichier : %s (ligne %d)\nTrace :\n%s",
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );
    }

    public function getFilePath(): string
    {
        return $this->file;
    }
}
