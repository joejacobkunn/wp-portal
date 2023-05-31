<?php

namespace App\Http\Livewire\Component;

use Rappasoft\LaravelLivewireTables\DataTableComponent as RappasoftDataTableComponent;

/** Extended Datatable Component */
abstract class DataTableComponent extends RappasoftDataTableComponent
{
    protected $selectedColumnFields;

    protected $selectedColumnHeaders;

    /**
     * Get selected columns table fields
     */
    public function getSelectedColumnFields()
    {
        if ($this->selectedColumnFields) {
        return $this->selectedColumnFields;
        }

        $fields = [];
        foreach ($this->columns as $column) {
            if (in_array($column->getHash(), $this->selectedColumns) && $column->getField()) {

                if ($column->isBaseColumn()) {
                    $fields[] = $column->getColumnSelectName();
                } else {
                    $fields[] = $column->getColumn();
                }
            }
        }

        return $this->selectedColumnFields = $fields;
    }

    /**
     * Get selected columns table headers
     */
    public function getSelectedColumnHeaders()
    {
        if ($this->selectedColumnHeaders) {
        return $this->selectedColumnHeaders;
        }

        $headers = [];

        foreach ($this->columns as $column) {
            if (in_array($column->getHash(), $this->selectedColumns) && $column->getField()) {
                $headers[] = $column->getTitle();
            }
        }

        return $this->selectedColumnHeaders = $headers;
    }

    /**
     * Get query builder with filters appied
     */
    public function getBaseQuery()
    {
        return $this->baseQuery();
    }
}
