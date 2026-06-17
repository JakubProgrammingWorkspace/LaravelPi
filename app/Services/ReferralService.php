<?php

namespace App\Services;

use App\Models\ExposureCategory;
use App\Models\ExposureFactor;
use App\Models\Referral;
use App\Models\ReferralExposureFactor;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReferralService
{
    /**
     * List all referrals with pagination.
     */
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Referral::with(['employee', 'creator'])
            ->orderByDesc('issue_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Create a new referral with exposure factors.
     */
    public function create(array $data): Referral
    {
        return \DB::transaction(function () use ($data) {
            $referral = Referral::create(array_merge($data, [
                'created_by' => auth()->id(),
            ]));

            // Attach exposure factors with details
            if (!empty($data['exposure_factor_ids'])) {
                foreach ($data['exposure_factor_ids'] as $index => $factorId) {
                    $details = $data['exposure_details'][$index] ?? null;
                    ReferralExposureFactor::create([
                        'referral_id'        => $referral->id,
                        'exposure_factor_id' => $factorId,
                        'exposure_details'   => $details,
                    ]);
                }
            }

            return $referral;
        });
    }

    /**
     * Generate a PDF for the referral.
     *
     * @return string The path to the generated PDF within storage.
     */
    public function generatePdf(Referral $referral): string
    {
        $referral->load(['employee.company', 'exposureFactors.exposureFactor.category']);

        // Build category sections: Group exposure factors by category
        $sections = [];
        foreach (['I', 'II', 'III', 'IV', 'V'] as $code) {
            $factors = [];
            foreach ($referral->exposureFactors as $refFactor) {
                $ef = $refFactor->exposureFactor;
                if ($ef && $ef->category && $ef->category->code === $code) {
                    $factors[] = [
                        'name'          => $ef->name,
                        'details'       => $refFactor->exposure_details,
                    ];
                }
            }
            if (!empty($factors)) {
                $category = ExposureCategory::where('code', $code)->first();
                $sections[] = [
                    'code'       => $code,
                    'name'       => $category ? $category->name : "Kategoria {$code}",
                    'factors'    => $factors,
                ];
            }
        }

        $totalFactors = count($referral->exposureFactors);

        // Employee data
        $employee = $referral->employee;
        $company = $employee->company;

        // Build HTML for PDF
        $html = $this->buildHtml($referral, $employee, $company, $sections, $totalFactors);

        // Create PDF
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Save PDF
        $filename = 'skierowanie-' . $referral->id . '-' . date('YmdHis') . '.pdf';
        $path = 'public/referrals/' . $filename;

        // Ensure directory exists
        $storagePath = storage_path('app/' . dirname($path));
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        file_put_contents(storage_path('app/' . $path), $dompdf->output());

        // Update referral record
        $referral->update([
            'pdf_path'        => $path,
            'pdf_generated_at' => now(),
        ]);

        return $path;
    }

    /**
     * Download the PDF. Returns the binary file response or 404.
     */
    public function downloadPdf(Referral $referral)
    {
        $path = $referral->pdf_path;

        if (is_null($path) || !file_exists(storage_path('app/' . $path))) {
            return response()->json(['error' => 'Nie wygenerowano jeszcze PDF dla tego skierowania.'], 404);
        }

        $filename = 'skierowanie-' . $referral->id . '.pdf';

        return response()->download(storage_path('app/' . $path), $filename);
    }

    /**
     * Delete a referral (cascades exposure factors).
     */
    public function delete(Referral $referral): bool
    {
        // Delete PDF file if exists
        if ($referral->pdf_path && file_exists(storage_path('app/' . $referral->pdf_path))) {
            @unlink(storage_path('app/' . $referral->pdf_path));
        }

        return $referral->delete();
    }

    /**
     * Build the HTML for the PDF.
     */
    private function buildHtml(Referral $referral, $employee, $company, array $sections, int $totalFactors): string
    {
        $companyName = $company ? $company->name : '';
        $companyAddress = $company ? $company->full_address : '';
        $typeLabel = match($referral->examination_type) {
            'wstępne'   => 'wstępne',
            'okresowe'   => 'okresowe',
            'kontrolne'  => 'kontrolne',
            default      => $referral->examination_type,
        };

        $addressParts = array_filter([$companyAddress]);
        $addressStr = implode(', ', $addressParts);
        $place = $referral->issue_place ?? '';
        $date = $referral->issue_date->format('d.m.Y');

        $fullName = $employee->full_name;
        $pesel = $employee->pesel ?? '';
        $employeeAddress = $employee->address ?? '';

        $jobPosition = $referral->job_position ?? '';
        $jobDesc = $referral->job_description ?? '';

        // Build table rows for factors
        $rowsHtml = '';
        foreach ($sections as $section) {
            foreach ($section['factors'] as $factor) {
                $details = $factor['details'] ? $factor['details'] : '—';
                $rowsHtml .= '<tr>';
                $rowsHtml .= '<td>' . $section['name'] . '</td>';
                $rowsHtml .= '<td>' . $factor['name'] . '</td>';
                $rowsHtml .= '<td>' . $details . '</td>';
                $rowsHtml .= '</tr>';
            }
        }

        $html = <<<HTML
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.4; margin: 20mm; }
        h1 { font-size: 16pt; text-align: center; margin-bottom: 5mm; }
        h2 { font-size: 13pt; text-align: center; margin-top: 3mm; margin-bottom: 2mm; }
        .header { text-align: right; margin-bottom: 5mm; font-size: 10pt; }
        .header .company { font-weight: bold; font-size: 12pt; }
        table { width: 100%; border-collapse: collapse; margin: 4mm 0; }
        th, td { border: 1px solid #333; padding: 4px 6px; font-size: 10pt; text-align: left; vertical-align: top; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .field { margin-bottom: 3mm; }
        .field-label { font-weight: bold; }
        .signature { margin-top: 15mm; }
        .signature-line { border-top: 1px solid #000; width: 200px; margin-top: 20px; }
    </style>
</head>
<body>

<div class="header">
    <div class="company">{$companyName}</div>
    <div>{$addressStr}</div>
    <div style="margin-top: 2mm;">Miejsce: {$place} &nbsp;&nbsp; Data: {$date}</div>
</div>

<h2>SKIEROWANIE NA BADANIA LEKARSKIE ({$typeLabel})</h2>

<div class="field">
    <span class="field-label">Dane pracownika:</span><br>
    {$fullName}, PESEL: {$pesel}<br>
    Adres: {$employeeAddress}
</div>

<div class="field">
    <span class="field-label">Stanowisko pracy:</span> {$jobPosition}<br>
    <span class="field-label">Opis warunków pracy:</span> {$jobDesc}
</div>

<h2>Czynniki narażenia zawodowego</h2>

<table>
    <tr>
        <th>Kategoria</th>
        <th>Nazwa czynnika</th>
        <th>Wielkość narażenia / wyniki pomiarów</th>
    </tr>
    {$rowsHtml}
</table>

<div class="field" style="margin-top: 5mm;">
    Liczba czynników łącznie: <strong>{$totalFactors}</strong>
</div>

<div class="signature">
    <div style="margin-bottom: 10px;">Podpis osoby kierującej pracownika na badania:</div>
    <div class="signature-line">&nbsp;</div>
</div>

</body>
</html>
HTML;

        return $html;
    }
}
