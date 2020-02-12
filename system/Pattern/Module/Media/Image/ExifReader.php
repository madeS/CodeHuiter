<?php

namespace CodeHuiter\Pattern\Module\Media\Image;

use CodeHuiter\Modifier\StringModifier;
use CodeHuiter\Service\Logger;
use Exception;

class ExifReader
{
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }


    /**
     * Получение Exif  информации из фотографии
     * @param string $file
     * @return array
     * longitude,  latitude, make, model, exposure, aperture, apertureValue,
     * iso, focalLength35mm, focalLength, meteringMode, flash, exposureBiasValue,
     * sensingMethod, gainControl, exposureProgram, maxApertureValue, datetime,
     * orientation
     */
    public function get(string $file){
        try {
            $size = getimagesize($file);
            if (!$size) return [];
            $type = $size['mime'] ?? '';
            if ($type !== 'image/jpeg') return [];
            if (!function_exists('exif_read_data')) return [];
            /** @noinspection PhpComposerExtensionStubsInspection */
            $exif_data = exif_read_data($file,'GPS,IFD0,EXIF',0);
            if (!is_array($exif_data)) return [];
            $ret = [];
            if (isset($exif_data['MakerNote'])) unset($exif_data['MakerNote']);
            foreach($exif_data as $key => $val){
                if (strpos($key,'UndefinedTag') !== false){
                    unset($exif_data[$key]);
                }
            }
            //echo '<!-- '; $this->debugParam($exif_data); echo ' -->';
            if (isset($exif_data['GPSLongitude'])){
                $ret['longitude'] = $this->exifrGetGps(
                    ($exif_data['GPSLongitude'] ?? []),
                    ($exif_data['GPSLongitudeRef'] ?? '')
                );
            } else {
                $ret['longitude'] = '';
            }
            if (isset($exif_data['GPSLatitude'])){
                $ret['latitude'] = $this->exifrGetGps(
                    ($exif_data['GPSLatitude'] ?? []),
                    ($exif_data['GPSLatitudeRef'] ?? '')
                );
            } else {
                $ret['latitude'] = '';
            }
            $ret['make'] = $exif_data['Make'] ?? '';
            $ret['model'] = $exif_data['Model'] ?? '';
            $ret['exposure'] = $this->exifr2Num(
                ($exif_data['ExposureTime'] ?? ''),
                '1/x'
            );
            $ret['aperture'] = $exif_data['COMPUTED']['ApertureFNumber'] ?? '';
            if (!$ret['aperture']) {
                $fNum = $exif_data['FNumber'] ?? '';
                if ($fNum) {
                    $ret['aperture'] = 'f/'. number_format((float)$this->exifr2Num($fNum), 1);
                }
            }
            if (isset($exif_data['ApertureValue'])){
                $ret['apertureValue'] = number_format((float)($this->exifr2Num($exif_data['ApertureValue'] ?? '')),3);
            } else {
                $ret['apertureValue'] = '';
            }
            $ret['iso'] = $exif_data['ISOSpeedRatings'] ?? '';
            $ret['focalLength35mm'] = $this->exifr2Num($exif_data['FocalLengthIn35mmFilm'] ?? '');
            $ret['focalLength'] = $this->exifr2Num($exif_data['FocalLength'] ?? '');
            $ret['meteringMode'] = $exif_data['MeteringMode'] ?? '';
            $ret['flash'] = $exif_data['Flash'] ?? '';
            $ret['exposureBiasValue'] = $exif_data['ExposureBiasValue'] ?? '';
            $ret['sensingMethod'] = $exif_data['SensingMethod'] ?? '';
            $ret['gainControl'] = $exif_data['GainControl'] ?? '';
            $ret['exposureProgram'] = $exif_data['ExposureProgram'] ?? '';
            if (isset($exif_data['ApertureValue'])){
                $ret['maxApertureValue'] = number_format((float)$this->exifr2Num($exif_data['MaxApertureValue'] ?? ''),3);
            } else {
                $ret['maxApertureValue'] = '';
            }
            $ret['datetime'] = $exif_data['DateTime'] ?? '';
            if (!$ret['datetime']) $ret['datetime'] = $exif_data['DateTimeOriginal'] ?? '';
            if (!$ret['datetime']) $ret['datetime'] = $exif_data['DateTimeDigitized'] ?? '';
            if ($ret['datetime']) $ret['datetime'] = $this->exifrFormatDate($ret['datetime']);
            $ret['orientation'] = $exif_data['Orientation'] ?? '';
            return $ret;
        } catch (Exception $exception) {
            $this->logger->withTag('EXIF_READER')->notice('Cant get exif data from file', [
                'file' => $file,
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
            return [];
        }

    }
    private function exifrFormatDate(string $date): string
    {
        $tdate = $date;
        if (mb_strlen($date)>10){
            $date1 = substr($date, 0, 10);
            $date2 = substr($date, 10);
            $date1 = str_replace(':', '-', $date1);
            $tdate = $date1 . $date2;
        }
        return StringModifier::dateConvert($tdate, 'm-m');
    }
    private function exifrGetGps(array $exifCoordcoordinate, string $hemi): float
    {
        $degrees = count($exifCoordcoordinate) > 0 ? $this->exifr2Num($exifCoordcoordinate[0]) : 0;
        $minutes = count($exifCoordcoordinate) > 1 ? $this->exifr2Num($exifCoordcoordinate[1]) : 0;
        $seconds = count($exifCoordcoordinate) > 2 ? $this->exifr2Num($exifCoordcoordinate[2]) : 0;
        $flip = ($hemi === 'W' or $hemi === 'S') ? -1 : 1;
        return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
    }
    private function exifr2Num(string $coordinatePart, string $type='') {
        $parts = explode('/', $coordinatePart);
        if (count($parts) <= 0) return '';
        if (count($parts) === 1) return $parts[0];
        if ($type === '1/x'){
            return '1/' . (int)($parts[1] / (float)($parts[0]));
        }
        return (float)$parts[0] / (float)$parts[1];
    }
}