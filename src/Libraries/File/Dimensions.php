<?php

namespace Quantum\Libraries\File;

class Dimensions extends \Upload\Validation\Base {

    /**
     * @var integer
     */
    protected $width;

    /**
     * @var integer
     */
    protected $height;

    /**
     * @param int $expectedWidth
     * @param int $expectedHeight
     */
    function __construct($expectedWidth, $expectedHeight) {
        $this->width = $expectedWidth;
        $this->height = $expectedHeight;
    }

    /**
     * Validate
     * @param  \Upload\File $file
     * @return bool
     */
    public function validate(\Upload\File $file) {
        $isValid = true;
        $dimensions = $file->getDimensions();
        $filename = $file->getNameWithExtension();
        if (!$dimensions) {
            $this->setMessage(sprintf('%s: Could not detect image size.', $filename));
            $isValid = false;
        }
        if ($dimensions['width'] != $this->width) {
            $this->setMessage(sprintf('Image width(%dpx) does not match required width(%dpx)', $dimensions['width'], $this->width));
            $isValid = false;
        }
        if ($dimensions['height'] != $this->height) {
            $this->setMessage(sprintf('Image height(%dpx) does not match required height(%dpx)', $dimensions['height'], $this->height));
            $isValid = false;
        }

        return $isValid;
    }

}
