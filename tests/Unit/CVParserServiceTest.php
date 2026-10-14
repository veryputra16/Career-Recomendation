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
        $text = 'John Doe - Software Engineer';
        $streamContent = "BT\n/F1 14 Tf\n50 700 Td\n({$text}) Tj\nET\n";
        $streamLen = strlen($streamContent);

        $obj1 = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $obj2 = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $obj3 = "3 0 obj\n<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 4 0 R >> >> /MediaBox [0 0 612 792] /Contents 5 0 R >>\nendobj\n";
        $obj4 = "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
        $obj5 = "5 0 obj\n<< /Length {$streamLen} >>\nstream\n{$streamContent}endstream\nendobj\n";

        $header = "%PDF-1.4\n";
        $off1 = strlen($header);
        $off2 = $off1 + strlen($obj1);
        $off3 = $off2 + strlen($obj2);
        $off4 = $off3 + strlen($obj3);
        $off5 = $off4 + strlen($obj4);
        $xrefOffset = $off5 + strlen($obj5);

        $xref = "xref\n0 6\n"
            . "0000000000 65535 f \n"
            . sprintf("%010d 00000 n \n", $off1)
            . sprintf("%010d 00000 n \n", $off2)
            . sprintf("%010d 00000 n \n", $off3)
            . sprintf("%010d 00000 n \n", $off4)
            . sprintf("%010d 00000 n \n", $off5);

        $trailer = "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF\n";

        $pdfContent = $header . $obj1 . $obj2 . $obj3 . $obj4 . $obj5 . $xref . $trailer;

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
