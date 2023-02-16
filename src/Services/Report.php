<?php

declare(strict_types=1);

namespace Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet as PHPSpreadSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

final class Report
{
    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws Exception
     */
    public static function generate(
        array  $records,
        array  $columnHeaderTitles,
        string $filePath,
        string $vendorName,
        string $range
    ): string {

        $spreadsheet = new PHPSpreadSheet();
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getProperties()
            ->setCreator('Ryser Media')
            ->setLastModifiedBy('Ryser Media')
            ->setTitle($vendorName)
            ->setSubject($vendorName)
            ->setDescription($vendorName)
            ->setKeywords($vendorName)
            ->setCategory('Report');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->setPreCalculateFormulas(false);

        // Remove default worksheet
        $spreadsheet->removeSheetByIndex(
            $spreadsheet->getIndex(
                $spreadsheet->getSheetByName('Worksheet')
            )
        );

        $spreadsheet->addSheet(new WorkSheet($spreadsheet, $vendorName), 0);
        $spreadsheet->setActiveSheetIndexByName($vendorName);

        $columns = range('A', $range);
        foreach ($columns as $column) {
            $spreadsheet->getActiveSheet()
                ->getColumnDimension($column)
                ->setAutoSize(true)
                ->setVisible(true);
        }

        $spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName('Calibri');
        $spreadsheet->getDefaultStyle()
            ->getFont()
            ->setSize(11);

        $headerColumnFillColor = 'AAAAAAAA';
        $headerColumnRange = 'A1:' . end($columns) . '1';
        $spreadsheet->getActiveSheet()
            ->getStyle($headerColumnRange)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB($headerColumnFillColor);
        $spreadsheet->getActiveSheet()
            ->getStyle($headerColumnRange)
            ->getFont()
            ->setBold(true);
        $spreadsheet->getActiveSheet()
            ->getStyle($headerColumnRange)
            ->applyFromArray([
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                ],
            ]);

        $columnRange = 'A:' . end($columns);
        $spreadsheet->getActiveSheet()
            ->getStyle($columnRange)
            ->getAlignment()
            ->setHorizontal('left');

        $spreadsheet->getActiveSheet()
            ->fromArray($columnHeaderTitles);

        $spreadsheet->getActiveSheet()
            ->fromArray(
                $records, // The data to set
                null, // Array values with this value will not be set
                'A2', // Top left coordinate of the worksheet range where we want to set these values (default is A1)
                true // To display actual value if not null i.e. '0'
            );

        $lastRow = $spreadsheet->getActiveSheet()->getHighestRow() + 1;
        $spreadsheet->getActiveSheet()->insertNewRowBefore($lastRow);

        $writer->save($filePath);

        return $filePath;
    }
}
