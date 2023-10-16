<?php
/**
 * Created by PhpStorm.
 * User: Giansalex
 * Date: 16/07/2017
 * Time: 12:23
 */

namespace Zip;

/**
 * Class ZipFactory
 * @package Greenter\Zip
 */
final class Reader
{
    const UNZIP_FORMAT = 'Vsig/vver/vflag/vmeth/vmodt/vmodd/Vcrc/Vcsize/Vsize/vnamelen/vexlen';

    /**
     * Retorna el contenido del primer xml dentro del zip.
     *
     * @param string $zipContent
     * @return array<string>
     */
    public static function get(string $zipContent)
    {
        $start = 0;
        $max = 10;
        while ($max > 0) {
            $dat = substr($zipContent, $start, 30);
            if (empty($dat)) {
                break;
            }

            $head = unpack(self::UNZIP_FORMAT, $dat);
            $filename = substr(substr($zipContent, $start),30, $head['namelen']);
            if (empty($filename)) {
                break;
            }
            $count = 30 + $head['namelen'] + $head['exlen'];

            if (strtolower(static::getFileExtension($filename)) == 'xml') {
                yield gzinflate(substr($zipContent, $start + $count, $head['csize']));
            }

            $start += $count + $head['csize'];
            $max--;
        }
    }

    private static function getFileExtension(string $filename): string
    {
        $lastDotPos = strrpos($filename, '.');
        if (!$lastDotPos) return '';

        return substr($filename, $lastDotPos + 1);
    }
}
