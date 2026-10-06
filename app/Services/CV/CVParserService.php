<?php

namespace App\Services\CV;

use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Smalot\PdfParser\Parser;
use Throwable;

class CVParserService
{
    protected Parser $parser;

    public function __construct(?Parser $parser = null)
    {
        $this->parser = $parser ?? new Parser();
    }

    /**
     * Extract and clean text content from a PDF file.
     *
     * @param string $filePath Absolute path to the PDF file.
     * @return string Extracted and normalized text.
     * @throws InvalidArgumentException When the file does not exist.
     * @throws Throwable When PDF parsing encounters an unrecoverable error.
     */
    public function extractText(string $filePath): string
    {
        if (! file_exists($filePath)) {
            throw new InvalidArgumentException("PDF file not found at: {$filePath}");
        }

        try {
            $pdf = $this->parser->parseFile($filePath);
            $rawText = $pdf->getText();

            return $this->cleanText($rawText);
        } catch (Throwable $e) {
            Log::warning("PDF text extraction failed: {$e->getMessage()}", [
                'path' => $filePath,
                'exception' => get_class($e),
            ]);

            throw $e;
        }
    }

    /**
     * Clean and normalize extracted text for downstream processing.
     */
    public function cleanText(string $text): string
    {
        // Normalize line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // Remove control characters except standard whitespace (\n, \t)
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text) ?? $text;

        // Collapse multiple horizontal whitespaces into single space
        $text = preg_replace('/[^\S\n]+/', ' ', $text) ?? $text;

        // Reduce 3+ consecutive newlines to at most 2
        $text = preg_replace('/\n{3,}/', "\n\n", $text) ?? $text;

        return trim($text);
    }
}
