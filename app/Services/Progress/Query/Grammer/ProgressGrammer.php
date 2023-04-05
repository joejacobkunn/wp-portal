<?php

namespace App\Services\Progress\Query\Grammer;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Support\Str;

class ProgressGrammer extends Grammar
{
    /**
     * @var string|null
     */
    protected $schema = null;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        // We are setting the schema here in order we can prefix it before a table name.
        // Check if we end with a dot if not add the dot at the end.
        if (isset($config['schema'])) {
            $this->schema = Str::finish($config['schema'], '.');
        }
    }

    /**
     * Compile the "from" portion of the query.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string                             $table
     *
     * @return string
     */
    protected function compileFrom(Builder $query, $table)
    {
        return 'from '.($this->schema ?? '').$this->wrapTable($table);
    }

    /**
     * Compile the "limit" portions of the query.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param int                                $limit
     *
     * @return string
     */
    protected function compileLimit(Builder $query, $limit)
    {
        return '';
    }

    /**
     * Compile the "select *" portion of the query.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param array                              $columns
     *
     * @return string|null
     */
    protected function compileColumns(Builder $query, $columns)
    {
        // If the query is actually performing an aggregating select, we will let that
        // compiler handle the building of the select clauses, as it will need some
        // more syntax that is best handled by that function to keep things neat.
        if (!is_null($query->aggregate)) {
            return;
        }

        if ($query->distinct) {
            $select = 'select distinct ';
        } else {
            $select = 'select ';
        }

        if ($query->limit) {
            // Only use the TOP sql when there is no OFFSET specified, otherwise we get errors.
            if (!isset($query->offset)) {
                $select .= 'top '.(int) $query->limit.' ';
            }
        }

        return $select.$this->columnize($columns);
    }

    /**
     * Compile the "join" portions of the query.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param array                              $joins
     *
     * @return string
     */
    protected function compileJoins(Builder $query, $joins)
    {
        return collect($joins)->map(function ($join) use ($query) {
            $table = ($this->schema ?? '').$this->wrapTable($join->table);

            $nestedJoins = is_null($join->joins) ? '' : ' '.$this->compileJoins($query, $join->joins);

            $tableAndNestedJoins = is_null($join->joins) ? $table : '('.$table.$nestedJoins.')';

            return trim("{$join->type} join {$tableAndNestedJoins} {$this->compileWheres($join)}");
        })->implode(' ');
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param mixed                              $offset
     *
     * @return void
     */
    protected function compileOffset(Builder $query, $offset)
    {
        // When the programmer also specified a limit we are going to OFFSET first and FETCH x amount next
        // When the programmer did not specify any limit we just OFFSET.
        if (isset($query->limit)) {
            return 'OFFSET '.$offset.' ROWS FETCH NEXT '.$query->limit.' ROWS ONLY';
        }

        return 'OFFSET '.$offset.' ROW';
    }
}
