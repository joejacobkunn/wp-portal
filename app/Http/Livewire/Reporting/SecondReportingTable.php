<?php

namespace App\Http\Livewire\Reporting;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Report\Report;
use App\Models\Report\SecondReporting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Rappasoft\LaravelLivewireTables\Views\Column;
use PHPSQLParser\PHPSQLParser;

class SecondReportingTable extends DataTableComponent
{
    use AuthorizesRequests;

    public $query;

    public $broadcast = false;

    private $colors = [ 'success', 'warning'];

    private $color_toggle = 1;

    public $groupby;

    private $column_tally_ignore = [
        'CompanyID', 'CONO', 'CustNo', 'CustomerNumber', 'OrderNumber', 'OrderSuffix'
    ];

    public function configure(): void
    {
        $this->setPrimaryKey('id');

        if(!empty($this->groupby)) $this->setDefaultSort($this->groupby, 'desc');

        if($this->broadcast){
            $this->setSearchDisabled();
            $this->setColumnSelectDisabled();
            $this->setPerPageVisibilityDisabled();
        } 


        $this->setPerPageAccepted([50, 75, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);

        $this->setTrAttributes(function($row, $index) {
            if(!empty($this->groupby))
            {
                if (!array_key_exists($row->{$this->groupby}, $this->colors)) {
                    $this->color_toggle = !$this->color_toggle;
                    $this->colors[$row->{$this->groupby}] = $this->colors[$this->color_toggle];
                }

                return [
                    'class' => 'table-'.$this->colors[$row->{$this->groupby}],
                ];
      
            }
       
            return [];
        });

    }

    public function boot(): void
    {

    }

    public function columns(): array
    {
        $columns = [];
        $parser = new PHPSQLParser($this->cleanWithNoLocks($this->query, true));
        $parsed_columns = $parser->parsed;

        foreach($parsed_columns['SELECT'] as $column){
            if(is_array($column['alias'])) $column_name = $this->clean($column['alias']['name']);
            else $column_name = $this->clean(end($column['no_quotes']['parts']));
            $columns[] =  Column::make($column_name, $column_name)->sortable()->searchable()->secondaryHeader(function($rows) use($column_name) {
                if(in_array($column_name, $this->column_tally_ignore)) return;
                $sum = 0;
                foreach($rows as $row)
                {
                    if(is_numeric($row->$column_name)){
                        $sum += $row->$column_name;
                    }
                }

                if($sum > 0) return '<a href="#" class="badge bg-primary">Total : '.$sum.'</a>';
            })->html();
        }

        return $columns;

    }

    public function filters(): array
    {
        return [
            
        ];
    }

    public function builder(): Builder
    {
        return SecondReporting::setQuery($this->query);
    }

    private function clean($string)
    {
        $string = str_replace('"','',$string);
        $string = str_replace("'",'',$string);
        return $string;
    }

    private function cleanWithNoLocks($string)
    {
        $string = str_replace('WITH (NOLOCK)','',$string);
        $string = str_replace("WITH(NOLOCK)",'',$string);
        $string = str_replace('with (nolock)','',$string);
        $string = str_replace("with(nolock)",'',$string);

        return $string;
    }

}
