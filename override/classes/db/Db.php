<?php

abstract class Db extends DbCore{
    
    /**
     * Executes an INSERT query
     *
     * @param string $table Table name without prefix
     * @param array $data Data to insert as associative array. If $data is a list of arrays, multiple insert will be done
     * @param bool $null_values If we want to use NULL values instead of empty quotes
     * @param bool $use_cache
     * @param int $type Must be Db::INSERT or Db::INSERT_IGNORE or Db::REPLACE
     * @param bool $add_prefix Add or not _DB_PREFIX_ before table name
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function insert($table, $data, $null_values = false, $use_cache = true, $type = Db::INSERT, $add_prefix = true)
    {
        if (!$data && !$null_values) {
            return true;
        }

        if ($add_prefix) {
            $table = _DB_PREFIX_.$table;
        }

        if ($type == Db::INSERT) {
            $insert_keyword = 'INSERT';
        } elseif ($type == Db::INSERT_IGNORE) {
            $insert_keyword = 'INSERT IGNORE';
        } elseif ($type == Db::REPLACE) {
            $insert_keyword = 'REPLACE';
        } elseif ($type == Db::ON_DUPLICATE_KEY) {
            $insert_keyword = 'INSERT';
        } else {
            throw new PrestaShopDatabaseException('Bad keyword, must be Db::INSERT or Db::INSERT_IGNORE or Db::REPLACE');
        }

        // Check if $data is a list of row
        $current = current($data);
        if (!is_array($current) || isset($current['type'])) {
            $data = array($data);
        }

        $keys = array();
        $values_stringified = array();
        $first_loop = true;
        $duplicate_key_stringified = '';
        foreach ($data as $row_data) {
            $values = array();
            foreach ($row_data as $key => $value) {
                if (!$first_loop) {
                    // Check if row array mapping are the same
                    if (!in_array("`$key`", $keys)) {
                        throw new PrestaShopDatabaseException('Keys form $data subarray don\'t match');
                    }

                    if ($duplicate_key_stringified != '') {
                        throw new PrestaShopDatabaseException('On duplicate key cannot be used on insert with more than 1 VALUE group');
                    }
                } else {
                    $keys[] = '`'.bqSQL($key).'`';
                }

                if (!is_array($value)) {
                    $value = array('type' => 'text', 'value' => $value);
                }
                if ($value['type'] == 'sql') {
                    $values[] = $string_value = $value['value'];
                } else {
                    $values[] = $string_value = $null_values && (is_null($value['value'])) ? 'NULL' : "'{$value['value']}'";
                }

                if ($type == Db::ON_DUPLICATE_KEY) {
                    $duplicate_key_stringified .= '`'.bqSQL($key).'` = '.$string_value.',';
                }
            }
            $first_loop = false;
            $values_stringified[] = '('.implode(', ', $values).')';
        }
        $keys_stringified = implode(', ', $keys);

        $sql = $insert_keyword.' INTO `'.$table.'` ('.$keys_stringified.') VALUES '.implode(', ', $values_stringified);
        if ($type == Db::ON_DUPLICATE_KEY) {
            $sql .= ' ON DUPLICATE KEY UPDATE '.substr($duplicate_key_stringified, 0, -1);
        }

        return (bool)$this->q($sql, $use_cache);
    }
}
