<?php

namespace OffbeatWP\Twig\Objects;

final readonly class OffbeatImageSrc
{
    public string $url;
    public int $width;
    public int $height;
    public bool $resized;

    /** @param array{0: string, 1: int, 2: int, 3: bool} $imgData */
    public function __construct(array $imgData)
    {
        $this->url = $imgData[0];
        $this->width = $imgData[1];
        $this->height = $imgData[2];
        $this->resized = $imgData[3];
    }
}