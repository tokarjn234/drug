<?php
namespace App\Models;

trait TDataRender
{

    public static function getRenderSettings() {
        return [];
    }
	
	
    public static function render ($data, $single = false) {
        $settings = self::getRenderSettings();
		
        if (is_object($data)) {
            try {
                $data = $data->toArray()['data'];
            } catch (\Exception $e) {

            }
        }

		if ($single) {
			$data = [$data];
		}
		
        foreach ($data as &$item) {
            $cloneItem = $item;
            foreach ($settings as $field => $options) {

                if (is_callable($options)) {
                    if (isset ($item[$field]) || array_key_exists($field, $item)) {
                        $item[$field] = $options($item[$field], $cloneItem);
                    } else {
                        $item[$field] = $options($cloneItem);
                    }
                } else if (is_array($options)) {
                    foreach ($options as $k => $v) {
                        if (is_callable($v)) {
                            $item[$field][$k] = $v($cloneItem);
                        }
                    }
                }
            }
        }

        return $single ? $data[0] : $data;
    }
}