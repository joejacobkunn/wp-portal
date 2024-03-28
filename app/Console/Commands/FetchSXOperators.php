<?php

namespace App\Console\Commands;

use App\Models\Core\Operator as CoreOperator;
use App\Models\SX\Operator;
use Illuminate\Console\Command;

class FetchSXOperators extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sx:fetch-operators';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command syncs sx operators to local mysql database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sx_operators = Operator::all();

        foreach($sx_operators as $sx_operator)
        {
            CoreOperator::updateOrCreate(
                ['cono' => $sx_operator->cono, 'operator' => $sx_operator->operinit],
                ['name' => $sx_operator->name, 'email' => $sx_operator->email]
            );
        }
    }
}
