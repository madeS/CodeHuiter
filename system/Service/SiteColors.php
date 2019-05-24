<?php

namespace CodeHuiter\Service;

use CodeHuiter\Exception\TagException;

class SiteColors
{
    public function __construct() {
        foreach ($this as $key => $value) {
            if (strpos($value, '~') === 0) {
                $fromKey = substr($value, 1);
                $this->$key = $this->$fromKey;
            }
        }
    }

    public function colorConvert($color, $opacity = 1){
        if (is_string($color)){
            if ($color[0] == '#') $color = substr($color, 1 );
            if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
            } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
            } else {
                return 'rgb(0,0,0)';
            }
            $rgb = array_map('hexdec', $hex);
            if ($opacity === false) {
                return 'rgb('.implode(",",$rgb).')';
            } else {
                return 'rgba('.implode(",",$rgb).','.$opacity.')';
            }
        } else if (is_array($color)) {
            $rgb = array_map('dechex', $color);
            return '#'.$rgb[0].$rgb[1].$rgb[2];
        } else {
            throw new TagException(
                'SiteColorService',
                'Cant convert color' . substr(print_r($color,true),1000)
            );
        }
    }
}
