<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\BaseBuilder;

class Basic extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Generate where clause from array
     */
    public function generate_where_clause($where, $builder = null)
    {
        if (empty($where)) return;
        
        // If no builder provided, create one (for backward compatibility)
        if ($builder === null) {
            $builder = $this->db->table('dummy'); // Temporary table, will be overridden
        }

        $keys = array_keys($where);

        for ($i = 0; $i < count($keys); $i++) {
            if ($keys[$i] == 'where') {
                if (is_array($where['where'])) {
                    foreach ($where['where'] as $key => $value) {
                        $builder->where($key, $value);
                    }
                } else {
                    $builder->where($where['where']);
                }
            } else if ($keys[$i] == 'where_in') {
                $keys_inner = array_keys($where['where_in']);
                for ($j = 0; $j < count($keys_inner); $j++) {
                    $field = $keys_inner[$j];
                    $value = $where['where_in'][$keys_inner[$j]];
                    $builder->whereIn($field, $value);
                }
            } else if ($keys[$i] == 'where_not_in') {
                $keys_inner = array_keys($where['where_not_in']);
                for ($j = 0; $j < count($keys_inner); $j++) {
                    $field = $keys_inner[$j];
                    $value = $where['where_not_in'][$keys_inner[$j]];
                    $builder->whereNotIn($field, $value);
                }
            } else if ($keys[$i] == 'or_where') {
                if (is_array($where['or_where'])) {
                    foreach ($where['or_where'] as $key => $value) {
                        $builder->orWhere($key, $value);
                    }
                } else {
                    $builder->orWhere($where['or_where']);
                }
            } else if ($keys[$i] == 'or_where_advance') {
                $keys_inner = array_keys($where['or_where_advance']);
                for ($j = 0; $j < count($keys_inner); $j++) {
                    $field = $where['or_where_advance'][$keys_inner[$j]];
                    $value = $keys_inner[$j];
                    $builder->orWhere($field, $value);
                }
            } else if ($keys[$i] == 'or_where_in') {
                $keys_inner = array_keys($where['or_where_in']);
                for ($j = 0; $j < count($keys_inner); $j++) {
                    $field = $keys_inner[$j];
                    $value = $where['or_where_in'][$keys_inner[$j]];
                    $builder->orWhereIn($field, $value);
                }
            } else if ($keys[$i] == 'or_where_not_in') {
                $keys_inner = array_keys($where['or_where_not_in']);
                for ($j = 0; $j < count($keys_inner); $j++) {
                    $field = $keys_inner[$j];
                    $value = $where['or_where_not_in'][$keys_inner[$j]];
                    $builder->orWhereNotIn($field, $value);
                }
            }
        }
    }

    /**
     * Generate joining clause from array
     */
    public function generate_joining_clause($join, $builder = null)
    {
        if (empty($join)) return;
        
        // If no builder provided, create one (for backward compatibility)
        if ($builder === null) {
            $builder = $this->db->table('dummy'); // Temporary table, will be overridden
        }

        $keys = array_keys($join);
        for ($i = 0; $i < count($join); $i++) {
            $join_table = $keys[$i];
            $join_condition_type = explode(',', $join[$keys[$i]]);
            $join_condition = $join_condition_type[0];
            $join_type = $join_condition_type[1] ?? 'inner';

            $builder->join($join_table, $join_condition, $join_type);
        }
    }

    /**
     * Get data from table
     */
    public function get_data($table, $where = '', $select = '', $join = '', $limit = '', $start = NULL, $order_by = '', $group_by = '', $num_rows = 0, $csv = '')
    {
        $builder = $this->db->table($table);

        if (!empty($select)) {
            if (is_array($select)) {
                $builder->select(implode(',', $select));
            } else {
                $builder->select($select);
            }
        } else {
            $builder->select('*');
        }

        if ($join != '') $this->generate_joining_clause($join);
        if ($where != '') $this->generate_where_clause($where);

        // Check if deleted field exists
        if ($this->db->fieldExists('deleted', $table)) {
            $deleted_str = $table . ".deleted";
            $builder->where($deleted_str, "0");
        }

        if ($order_by != '') $builder->orderBy($order_by);
        if ($group_by != '') $builder->groupBy($group_by);

        if (is_numeric($start) || is_numeric($limit)) {
            // Convert to integer for CI4 compatibility
            $limit_int = is_numeric($limit) ? (int)$limit : null;
            $start_int = is_numeric($start) ? (int)$start : null;
            if ($limit_int !== null) {
                $builder->limit($limit_int, $start_int);
            }
        }

        $query = $builder->get();

        if ($csv == 1) {
            return $query; // For CSV generation
        }

        $result_array = $query->getResultArray();

        if ($num_rows == 1) {
            $num_rows = $query->getNumRows();
            $result_array['extra_index'] = array('num_rows' => $num_rows);
        }

        return $result_array;
    }

    /**
     * Count rows from table
     */
    public function count_row($table, $where = '', $count = 'id', $join = '', $group_by = '')
    {
        $builder = $this->db->table($table);
        $builder->select($count);

        if ($join != '') $this->generate_joining_clause($join, $builder);
        if ($where != '') $this->generate_where_clause($where, $builder);

        if ($this->db->fieldExists('deleted', $table)) {
            $deleted_str = $table . ".deleted";
            $builder->where($deleted_str, "0");
        }

        if ($group_by != '') $builder->groupBy($group_by);

        $query = $builder->get();
        $num_rows = $query->getNumRows();

        $result_array[0]['total_rows'] = $num_rows;

        return $result_array;
    }

    /**
     * Insert data into table
     */
    public function insert_data($table, $data)
    {
        $builder = $this->db->table($table);
        $builder->insert($data);
        return true;
    }

    /**
     * Update data in table
     */
    public function update_data($table, $where, $data)
    {
        $builder = $this->db->table($table);
        if ($where != '') {
            if (is_array($where)) {
                $builder->where($where);
            } else {
                // Handle string where clause
                $builder->where($where);
            }
        }
        $builder->update($data);
        return true;
    }

    /**
     * Delete data from table
     */
    public function delete_data($table, $where)
    {
        $builder = $this->db->table($table);
        $builder->where($where);
        $builder->delete();
        return true;
    }

    /**
     * Execute custom SQL query
     */
    public function execute_query($sql)
    {
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }

    /**
     * Execute complex custom SQL query
     */
    public function execute_complex_query($sql)
    {
        return $this->db->query($sql);
    }

    /**
     * Check if row is active
     */
    public function is_active($table, $where = '')
    {
        $builder = $this->db->table($table);
        $builder->select('status');
        if (is_array($where)) {
            $where['status'] = 1;
            $builder->where($where);
        } else {
            $builder->where('status', 1);
            if ($where != '') $builder->where($where);
        }
        $query = $builder->get();
        $num_rows = $query->getNumRows();

        if ($num_rows > 0) return true;
        else return false;
    }

    /**
     * Check if row exists
     */
    public function is_exist($table, $where = '', $select = '')
    {
        $builder = $this->db->table($table);
        if ($select != '') {
            if (is_array($select)) {
                $builder->select(implode(',', $select));
            } else {
                $builder->select($select);
            }
        }
        if ($where != '') $builder->where($where);
        $query = $builder->get();
        $num_rows = $query->getNumRows();

        if ($num_rows > 0) return TRUE;
        else return FALSE;
    }

    /**
     * Check if row is unique
     */
    public function is_unique($table, $where = '', $select = '')
    {
        $builder = $this->db->table($table);
        if ($select != '') {
            if (is_array($select)) {
                $builder->select(implode(',', $select));
            } else {
                $builder->select($select);
            }
        }
        if ($where != '') $builder->where($where);
        $query = $builder->get();
        $num_rows = $query->getNumRows();

        if ($num_rows > 0) return FALSE;
        else return TRUE;
    }

    /**
     * Get enum values from table column
     */
    public function get_enum_values($table_name = "", $column_name = "")
    {
        $empty_array = array();

        if ($table_name == "" || $column_name == "")
            return $empty_array;

        $sql = "SHOW COLUMNS FROM $table_name WHERE Field = '$column_name'";
        $results = $this->execute_query($sql);

        if (empty($results)) return $empty_array;

        $enumList = explode(",", str_replace("'", "", substr($results[0]['Type'], 5, (strlen($results[0]['Type']) - 6))));
        return $enumList;
    }

    /**
     * Get enum values as associative array
     */
    public function get_enum_values_assoc($table_name = "", $column_name = "")
    {
        if ($table_name == "" || $column_name == "")
            return array();

        $sql = "SHOW COLUMNS FROM $table_name WHERE Field = '$column_name'";
        $results = $this->execute_query($sql);

        if (empty($results)) return array();

        $enumList = explode(",", str_replace("'", "", substr($results[0]['Type'], 5, (strlen($results[0]['Type']) - 6))));

        $enumList_final = array();
        foreach ($enumList as $key => $value) {
            $enumList_final[$value] = $value;
        }
        return $enumList_final;
    }

    /**
     * Import database dump
     */
    public function import_dump($filename = '')
    {
        if ($filename == '') {
            return false;
        }
        if (!file_exists($filename)) {
            return false;
        }

        $templine = '';
        $lines = file($filename);

        foreach ($lines as $line) {
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }

            $templine .= $line;
            if (substr(trim($line), -1, 1) == ';') {
                $this->execute_complex_query($templine);
                $templine = '';
            }
        }
        return true;
    }
}

