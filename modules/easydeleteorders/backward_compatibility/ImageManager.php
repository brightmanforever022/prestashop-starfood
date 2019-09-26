<?php

class ImageManager
{
    const ERROR_FILE_NOT_EXIST = 1;
    const ERROR_FILE_WIDTH     = 2;
    const ERROR_MEMORY_LIMIT   = 3;

    /**
     * Generate a cached thumbnail for object lists (eg. carrier, order statuses...etc)
     *
     * @param string $image Real image filename
     * @param string $cache_image Cached filename
     * @param int $size Desired size
     * @param string $image_type Image type
     * @param bool $disable_cache When turned on a timestamp will be added to the image URI to disable the HTTP cache
     * @param bool $regenerate When turned on and the file already exist, the file will be regenerated
     * @return string
     */
    public static function thumbnail($image, $cache_image, $size, $image_type = 'jpg', $disable_cache = true, $regenerate = false)
    {
        if (!file_exists($image)) {
            return '';
        }

        if (file_exists(_PS_TMP_IMG_DIR_.$cache_image) && $regenerate) {
            @unlink(_PS_TMP_IMG_DIR_.$cache_image);
        }

        if ($regenerate || !file_exists(_PS_TMP_IMG_DIR_.$cache_image)) {
            $infos = getimagesize($image);

            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($image)) {
                return false;
            }

            $x = $infos[0];
            $y = $infos[1];
            $max_x = $size * 3;

            // Size is already ok
            if ($y < $size && $x <= $max_x) {
                copy($image, _PS_TMP_IMG_DIR_.$cache_image);
            }
            // We need to resize */
            else {
                $ratio_x = $x / ($y / $size);
                if ($ratio_x > $max_x) {
                    $ratio_x = $max_x;
                    $size = $y / ($x / $max_x);
                }

                ImageManager::resize($image, _PS_TMP_IMG_DIR_.$cache_image, $ratio_x, $size, $image_type);
            }
        }
        // Relative link will always work, whatever the base uri set in the admin
        if (Context::getContext()->controller->controller_type == 'admin') {
            return '<img src="../img/tmp/'.$cache_image.($disable_cache ? '?time='.time() : '').'" alt="" class="imgm img-thumbnail" />';
        } else {
            return '<img src="'._PS_TMP_IMG_.$cache_image.($disable_cache ? '?time='.time() : '').'" alt="" class="imgm img-thumbnail" />';
        }
    }
 }
