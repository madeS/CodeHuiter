<?php

namespace CodeHuiter\Pattern\Module\Media\Image\Options;

class WatermarkTextOptions implements WatermarkOptions
{
    /** @var string */
    private $text;

    /** @var string */
    private $fontFile;

    /** @var int */
    private $fontSize;

    /** @var array */
    private $fontColor;

    /**
     * @param string $text
     * @param string $fontFile
     * @param int $fontSize
     * @param array $fontColor Like [255,255,255]
     */
    public function __construct(string $text, string $fontFile, int $fontSize = 5, array $fontColor = [255, 255, 255])
    {
        $this->text = $text;
        $this->fontFile = $fontFile;
        $this->fontSize = $fontSize;
        $this->fontColor = $fontColor;
    }

    public function getFontFile(): string
    {
        return $this->fontFile;
    }

    public function getFontSize(): int
    {
        return $this->fontSize;
    }

    public function getFontColor(): array
    {
        return $this->fontColor;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
