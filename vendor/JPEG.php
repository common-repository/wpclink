<?php
/***
 * Get JPEF Image Data
 * 
 * @param sting $filename filename
 * 
 * @return boolean | data
 */
function wpclink_get_jpeg_image_data($filename) {
    ignore_user_abort(true);
    $filehnd = @fopen($filename, 'rb');
    if (!$filehnd) {
        return FALSE;
    }
    $data = wpclink_network_safe_fread($filehnd, 2);
    if ($data != "\xFF\xD8") {
        fclose($filehnd);
        return FALSE;
    }
    $data = wpclink_network_safe_fread($filehnd, 2);
    if ($data[0] != "\xFF") {
        fclose($filehnd);
        return;
    }
    $hit_compressed_image_data = FALSE;
    while (($data[1] != "\xD9") && (!$hit_compressed_image_data) && (!feof($filehnd))) {
        if ((ord($data[1]) < 0xD0) || (ord($data[1]) > 0xD7)) {
            $sizestr     = wpclink_network_safe_fread($filehnd, 2);
            $decodedsize = unpack("nsize", $sizestr);
            $segdata     = wpclink_network_safe_fread($filehnd, $decodedsize['size'] - 2);
        }
        if ($data[1] == "\xDA") {
            $hit_compressed_image_data = TRUE;
            $compressed_data           = "";
            do {
                $compressed_data .= wpclink_network_safe_fread($filehnd, 1048576);
            } while (!feof($filehnd));
            $EOI_pos         = strpos($compressed_data, "\xFF\xD9");
            $compressed_data = substr($compressed_data, 0, $EOI_pos);
        } else {
            $data = wpclink_network_safe_fread($filehnd, 2);
            if ($data[0] != "\xFF") {
                fclose($filehnd);
                return;
            }
        }
    }
    fclose($filehnd);
    ignore_user_abort(false);
    if ($hit_compressed_image_data) {
        return $compressed_data;
    } else {
        return FALSE;
    }
}
/**
 * Network Safe File Read
 * 
 * @param sting $file_handle 
 * @param int $length 
 * 
 * @return string data
 */
function wpclink_network_safe_fread($file_handle, $length) {
    $data = "";
    while ((!feof($file_handle)) && (strlen($data) < $length)) {
        $data .= fread($file_handle, $length - strlen($data));
    }
    return $data;
}
