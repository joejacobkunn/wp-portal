<?php

namespace App\Console\Commands;

use App\Models\Core\Role;
use App\Models\Core\User;
use Illuminate\Console\Command;
use Microsoft\Graph\Graph;


class SyncAzureUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-azure-users {--title=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to sync Azure users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $titles = explode(",",$this->option('title'));

        foreach($titles as $title)
        {
            if(!empty($title))
            {
                $guzzle = new \GuzzleHttp\Client();
                $url = 'https://login.microsoftonline.com/' . config('services.azure.tenant') . '/oauth2/v2.0/token';
                $token = json_decode($guzzle->post($url, [
                    'form_params' => [
                        'client_id' => config('services.azure.client_id'),
                        'client_secret' => config('services.azure.client_secret'),
                        'scope' => 'https://graph.microsoft.com/.default',
                        'grant_type' => 'client_credentials',
                    ],
                ])->getBody()->getContents());
                $accessToken = $token->access_token;
        
                $graph = new Graph();
                $graph->setAccessToken($accessToken);
                $response = collect($graph->createRequest('GET', '/users?$filter=jobTitle eq \''.$title.'\'')->execute()->getBody());
    
                foreach($response['value'] as $azureUser)
                {

                    $user = User::updateOrCreate(
                        [
                            'email' => $azureUser['mail']
                        ],
                        [
                            'name' => $azureUser['displayName'],
                            'office_location' => $azureUser['officeLocation'],
                            'title' => $azureUser['jobTitle'],
                            'is_active' => 1,
                            'account_id' => 1,
                        ]
                    );
        
                    //Default Role Assign
                    $user->assignRole(Role::getDefaultRole());
                    $user->abbreviation = $user->getAbbreviation();
                    $user->save();
        
                }
            }
    
        }


    }
}
