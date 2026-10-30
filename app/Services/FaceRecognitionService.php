<?php

namespace App\Services;

class FaceRecognitionService
{
    /**
     * Hitung jarak Euclidean (L2 Distance) antara dua face descriptor 128-dimensi.
     *
     * @param array $descriptor1
     * @param array $descriptor2
     * @param float|null $threshold
     * @return array
     */
    public function compare(array $descriptor1, array $descriptor2, ?float $threshold = null): array
    {
        $threshold = $threshold ?? (float) config('services.face.threshold', 0.50);

        if (!$this->isValidDescriptor($descriptor1) || !$this->isValidDescriptor($descriptor2)) {
            return [
                'match'     => false,
                'distance'  => 1.0,
                'threshold' => $threshold,
                'error'     => 'Format descriptor wajah tidak valid (harus 128 angka float).',
            ];
        }

        $sumSquares = 0.0;
        $count = count($descriptor1);

        for ($i = 0; $i < $count; $i++) {
            $diff = (float) $descriptor1[$i] - (float) $descriptor2[$i];
            $sumSquares += ($diff * $diff);
        }

        $distance = sqrt($sumSquares);

        return [
            'match'     => $distance <= $threshold,
            'distance'  => round($distance, 4),
            'threshold' => $threshold,
        ];
    }

    /**
     * Validasi apakah array merupakan face descriptor yang sah (128 elemen float).
     *
     * @param mixed $descriptor
     * @return bool
     */
    public function isValidDescriptor(mixed $descriptor): bool
    {
        if (!is_array($descriptor) || count($descriptor) !== 128) {
            return false;
        }

        foreach ($descriptor as $val) {
            if (!is_numeric($val)) {
                return false;
            }
        }

        return true;
    }
}
