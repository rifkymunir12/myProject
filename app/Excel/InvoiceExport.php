<?php

namespace App\Excel;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class InvoiceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

        /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Customer',
            'Shipment',
            'Destination',
            'Price',
            'Payments',
            'Note',
        ];
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->customer?->name ?? '-',
            $row->shipment?->name ?? '-',
            $row->destination ?? '-',
            $row->final_price ?? '-',
            $row->payment ?? '-',
            $row->note ?? '-',
        ];
    }

    /**
     * @param mixed $sheet
     *
     * @return array
     */
    public function styles(Worksheet $sheet): array
    {
       return [
            1 =>  [
                'font' => ['bold' => true], 
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '555555']]]
                //'borders' => ['outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '555555']]]],
            ], 
            //'outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '555555']]], 
       ];
    }
}
