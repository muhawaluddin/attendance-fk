<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Imagick;
use ImagickPixel;

class LogoProcessor
{
    /**
     * Remove background from an uploaded logo image and save as transparent PNG.
     *
     * @param string $sourcePath Path to the source image file
     * @param string $targetPath Destination path for transparent PNG
     * @return bool
     */
    public static function removeBackground(string $sourcePath, string $targetPath): bool
    {
        if (class_exists(Imagick::class)) {
            try {
                $im = new Imagick($sourcePath);

                // Set PNG format and enable alpha channel
                $im->setImageFormat('png');
                $im->setImageAlphaChannel(Imagick::ALPHACHANNEL_SET);

                $w = $im->getImageWidth();
                $h = $im->getImageHeight();

                if ($w > 0 && $h > 0) {
                    $quantum = $im->getQuantumRange()['quantumRangeLong'];
                    // 18% fuzz tolerance cleanly removes JPEG compression noise around borders
                    $fuzz = 0.18 * $quantum;
                    $transparent = new ImagickPixel('transparent');

                    // Collect perimeter sample points along all 4 outer edges
                    $samplePoints = [];
                    for ($i = 0; $i <= 20; $i++) {
                        $step = $i / 20;
                        $samplePoints[] = [(int)(($w - 1) * $step), 0];          // Top edge
                        $samplePoints[] = [(int)(($w - 1) * $step), $h - 1];     // Bottom edge
                        $samplePoints[] = [0, (int)(($h - 1) * $step)];          // Left edge
                        $samplePoints[] = [$w - 1, (int)(($h - 1) * $step)];     // Right edge
                    }

                    // Flood fill starting from each opaque perimeter point
                    foreach ($samplePoints as $pt) {
                        $pixel = $im->getImagePixelColor($pt[0], $pt[1]);
                        $color = $pixel->getColor(true);
                        // Only flood fill if this point is not already transparent
                        if ($color['a'] > 0.05) {
                            $im->floodFillPaintImage($transparent, $fuzz, $pixel, $pt[0], $pt[1], false);
                        }
                    }

                    $im->writeImage($targetPath);
                    $im->clear();
                    $im->destroy();
                    return true;
                }
            } catch (Exception $e) {
                Log::warning('LogoProcessor background removal error: ' . $e->getMessage());
            }
        }

        // Fallback: If Imagick is not available or throws error, copy source file
        if ($sourcePath !== $targetPath) {
            copy($sourcePath, $targetPath);
        }

        return true;
    }
}
