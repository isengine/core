<?php defined('isPROCESS') or die;

$data = json_decode(base64_decode($process -> data['default']), true);

//echo '<pre>' . print_r($data, true) . '</pre>';

echo dbUse('ratings', 'write', $data);

//exit;

?>