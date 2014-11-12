<?

function order($array, $by) {
    $result = array();
    foreach ($array as $val) {
        if (!is_array($val) || !key_exists($by, $val)) {
            continue;
        }
        end($result);
        $current = current($result);
        while ($current[$by] > $val[$by]) {
            $result[key($result)+1] = $current;
            prev($result);
            $current = current($result);
        }
        $result[key($result)+1] = $val;
    }
    return $result;
}