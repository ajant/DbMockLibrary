<?php

namespace DbMockLibrary;

use SimpleArrayLibrary\SimpleArrayLibrary;
use InvalidArgumentException;

class MockLibrary
{
    /**
     * @param array $table
     * @param string $column
     * @param bool  $ascending
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public static function orderTableByColumn(array $table, $column, $ascending = true)
    {
        if (empty($table)) {
            return $table;
        }

        if (SimpleArrayLibrary::isAssociative($table)) {
            throw new InvalidArgumentException('No point in sorting non-numeric array');
        }

        for ($i = 0; $i < count($table) - 1; $i++) {
            for ($j = $i + 1; $j < count($table); $j++) {
                if (!isset($table[$i][$column])) {
                    throw new InvalidArgumentException("Sort column missing from row: '{$i}'");
                }
                if (!isset($table[$j][$column])) {
                    throw new InvalidArgumentException("Sort column missing from row: '{$j}'");
                }

                // sort
                if ($ascending) {
                    if ($table[$i][$column] > $table[$j][$column]) {
                        $tmp       = $table[$i];
                        $table[$i] = $table[$j];
                        $table[$j] = $tmp;
                    }
                } else {
                    if ($table[$i][$column] < $table[$j][$column]) {
                        $tmp       = $table[$i];
                        $table[$i] = $table[$j];
                        $table[$j] = $tmp;
                    }
                }
            }
        }

        return $table;
    }

    /**
     * @param array $table1
     * @param array $table2
     * @param array $joinColumns
     * @param array $selectColumns1
     * @param array $selectColumns2
     * @param bool  $distinct
     *
     * @throws InvalidArgumentException
     * @return array
     */
    public static function joinTables(array $table1, array $table2, array $joinColumns, array $selectColumns1, array $selectColumns2, $distinct = false)
    {
        // input validation
        if (empty($joinColumns)) {
            throw new InvalidArgumentException('No join condition provided');
        }
        if (empty($selectColumns1) && empty($selectColumns1)) {
            throw new InvalidArgumentException('No columns selected from the joining result');
        }
        $table1Invalid = count(SimpleArrayLibrary::getRectangularDimensions($table1)) != 2 && !empty($table1);
        $table2Invalid = count(SimpleArrayLibrary::getRectangularDimensions($table2)) != 2 && !empty($table2);
        if ($table1Invalid || $table2Invalid) {
            if ($table1Invalid && $table2Invalid) {
                throw new InvalidArgumentException('Neither of the tables is a rectangular 2 dimensional array');
            } elseif ($table1Invalid) {
                throw new InvalidArgumentException('Table one is not a rectangular 2 dimensional array');
            } else {
                throw new InvalidArgumentException('Table two is not a rectangular 2 dimensional array');
            }
        }
        if (!empty(array_intersect($selectColumns1, $selectColumns2))) {
            throw new InvalidArgumentException('Select columns must have no equal values, or columns in the result will be overwritten');
        }
        if (!is_bool($distinct)) {
            throw new InvalidArgumentException('Invalid distinct parameter');
        }

        if (empty($table1) || empty($table2)) {
            return [];
        }

        $return = [];
        foreach ($table1 as $key1 => $row1) {
            // input validation
            if (!SimpleArrayLibrary::isSubarray(array_keys($row1), array_keys($joinColumns))) {
                throw new InvalidArgumentException("Join column(s) missing from row {$key1} of first table");
            }
            if (!SimpleArrayLibrary::isSubarray(array_keys($row1), array_keys($selectColumns1))) {
                throw new InvalidArgumentException("Selected column(s) missing from row {$key1} of first table");
            }

            foreach ($table2 as $key2 => $row2) {
                // input validation
                if (!SimpleArrayLibrary::isSubarray(array_keys($row2), $joinColumns)) {
                    throw new InvalidArgumentException("Join column(s) missing from row {$key2} of second table");
                }
                if (!SimpleArrayLibrary::isSubarray(array_keys($row2), array_keys($selectColumns2))) {
                    throw new InvalidArgumentException("Selected column(s) missing from row {$key2} of second table");
                }

                // check join conditions
                $match = true;
                foreach ($joinColumns as $key => $value) {
                    if ($row1[$key] != $row2[$value]) {
                        $match = false;
                        break;
                    }
                }

                // join rows if all required columns are matched
                if ($match) {
                    $row = [];
                    // include columns from table 1
                    foreach ($selectColumns1 as $oldKey => $newKey) {
                        $row[$newKey] = $row1[$oldKey];
                    }
                    // include columns from table 2
                    foreach ($selectColumns2 as $oldKey => $newKey) {
                        $row[$newKey] = $row2[$oldKey];
                    }
                    $return[] = $row;
                }
            }
        }

        // remove duplicated rows
        if ($distinct) {
            $return = array_unique($return, SORT_REGULAR);
        }

        return $return;
    }

    /**
     * @param array        $table
     * @param string       $column
     * @param string       $condition
     * @param array|string $value
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public static function whereCondition(array $table, $column, $condition, $value)
    {
        // input validation
        if (!is_numeric($column)
            && (empty($column)
                || !is_string($column))
        ) {
            throw new InvalidArgumentException('Invalid column parameter');
        }

        if (count(SimpleArrayLibrary::getRectangularDimensions($table)) != 2) {
            if (empty($table)) {
                return [];
            } else {
                throw new InvalidArgumentException('Table is not a rectangular 2 dimensional array');
            }
        }

        $validConditions = [
            '>',
            '<',
            '=',
            '==',
            '>=',
            '<=',
            '<>',
            'in',
            'notin'
        ];
        $condition       = strtolower($condition);
        if (!in_array($condition, $validConditions)) {
            throw new InvalidArgumentException('Invalid condition parameter');
        }
        if (in_array($condition, ['in', 'notin'])
            && !is_array($value)
        ) {
            throw new InvalidArgumentException('In case of "in" & "notin" conditions, value must be an array');
        } elseif (!in_array($condition, ['in', 'notin'])
            && !is_numeric($value)
            && !is_string($value)
            && !is_null($value)
        ) {
            throw new InvalidArgumentException('Invalid value');
        }

        $return       = [];
        $tableNumeric = !SimpleArrayLibrary::isAssociative($table);
        foreach ($table as $index => $row) {
            if (!isset($row[$column])) {
                throw new InvalidArgumentException('Column missing from row: ' . $index);
            }

            $keep = true;
            switch ($condition) {
                case '>':
                    $keep = $row[$column] > $value;
                    break;
                case '<':
                    $keep = $row[$column] < $value;
                    break;
                case '>=':
                    $keep = $row[$column] >= $value;
                    break;
                case '<=':
                    $keep = $row[$column] <= $value;
                    break;
                case '=':
                case '==':
                    $keep = $row[$column] == $value;
                    break;
                case '<>':
                    $keep = $row[$column] <> $value;
                    break;
                case 'in':
                    $keep = in_array($row[$column], $value);
                    break;
                case 'notin':
                    $keep = !in_array($row[$column], $value);
                    break;
            }

            if ($keep) {
                if ($tableNumeric) {
                    $return[] = $row;
                } else {
                    $return[$index] = $row;
                }
            }
        }

        return $return;
    }
}