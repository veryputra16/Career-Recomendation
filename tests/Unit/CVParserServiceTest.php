<?php

namespace Tests\Unit;

use App\Services\CV\CVParserService;
use InvalidArgumentException;
use Tests\TestCase;

class CVParserServiceTest extends TestCase
{
    protected CVParserService $parserService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parserService = new CVParserService();
    }

    public function test_clean_text_normalizes_line_endings_and_spaces(): void
    {
        $input = "Line 1\r\n\r\n\r\n\r\nLine 2   with   extra    spaces.\rLine 3";
        $cleaned = $this->parserService->cleanText($input);

        $expected = "Line 1\n\nLine 2 with extra spaces.\nLine 3";
        $this->assertEquals($expected, $cleaned);
    }

    public function test_clean_text_removes_unprintable_control_characters(): void
    {
        $input = "Valid Text\x00\x08With Control\x1F Characters";
        $cleaned = $this->parserService->cleanText($input);

        $this->assertEquals("Valid TextWith Control Characters", $cleaned);
    }

    public function test_extract_text_throws_exception_if_file_does_not_exist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->parserService->extractText('/non/existent/file.pdf');
    }

    public function test_extract_text_from_valid_pdf_content(): void
    {
        // Construct a valid minimal text PDF
        $pdfContent = "%PDF-1.4\n"
            . "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n"
            . "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n"
            . "3 0 obj << /Type /Page /Parent 2 0 R /Resources << /Font << /F1 4 0 R >> >> /MediaBox [0 0 612 792] /Contents 5 0 R >> endobj\n"
            . "4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj\n"
            . "5 0 obj << /Length 52 >> stream\n"
            . "BT\n/F1 14 Tf\n50 700 Td\n(John Doe - Software Engineer) Tj\nET\n"
            . "endstream endobj\n"
            . "xref\n0 6\n"
            . "0000000000 65535 f \n"
            . "0000000009 00000 n \n"
            . "0000000058 00000 n \n"
            . "0000000115 00000 n \n"
            . "0000000236 00000 n \n"
            . "0000000305 00000 n \n"
            . "trailer << /Size 6 /Root 1 0 R >>\n"
            . "startxref\n410\n%%EOF\n";

        $tempFile = tempnam(sys_get_temp_dir(), 'pdf_test_') . '.pdf';
        file_put_contents($tempFile, $pdfContent);

        try {
            $extracted = $this->parserService->extractText($tempFile);
            $this->assertStringContainsString('John Doe - Software Engineer', $extracted);
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }
}
