<?php
//============================================================+
// File name   : image_clip.php
// Begin       : 2020-05-21
// Last Update : 2020-05-23
//
// Description : Generate an PDF with TCPDF
//
// Author: Andrian Voronka
//
//============================================================+

/**
 * Rotating rectangle image crop
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Process: Cropping an image with rotating rectangle mask
 * @author Andrian Voronka
 * @since 2020-05-21
 * 
 * @param TCPDF   PDF document
 * @param string  Image resource
 * @param int     Starting X position
 * @param int     Starting Y position
 * @param int     Clipping mask width
 * @param int     Clipping mask height
 * @param int     Rotating angle
 */
function image_rotating_rect_clip(
  $pdf,
  $image_src,
  $start_x,
  $start_y,
  $clip_width,
  $clip_height,
  $angle
) {

  $page_width = $pdf->getPageWidth();
  list($image_width, $image_height) = getimagesize($image_src);
  $image_height = $page_width * $image_height / $image_width;
  $image_width = $page_width;

  // PDF start transform
  $pdf->StartTransform();

  // Rotate rectangle mask first and rotate again.
  $pdf->Rotate($angle, $start_x + $image_width / 2, $start_y + $image_height / 2);
  $pdf->Rect($start_x + ($image_width - $clip_width) / 2, $start_y + ( $image_height - $clip_height ) / 2, $clip_width, $clip_height, 'CNZ');
  $pdf->Rotate(-$angle, $start_x + $image_width / 2, $start_y + $image_height / 2);
  $pdf->Image($image_src, $start_x, $start_y, $image_width, $image_height, '', true, '', false, 300);
  $pdf->StopTransform();
}

?>