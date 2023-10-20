<?php

namespace App\Console\Commands;

use App\Models\DanaToken;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckDanaTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:check:danaToken {--force= : force to renew the token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks expiration of Dana token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isForce = $this->option('force');
        $danaToken = DanaToken::all()->first();
        if (!isset($danaToken)) {
            Log::channel('danaTransfer')->debug('in CheckDanaTokenCommand: no Dana token found in dana_tokens table');
            return false;
        }

        $tokenExpiresAt = Carbon::parse($danaToken->expires_at, 'Asia/Tehran');
        $now = Carbon::now('Asia/Tehran');
        if ($now < $tokenExpiresAt && $tokenExpiresAt->diffInMinutes($now) > 690 && !isset($isForce)) {
            return false;
        }

        $refreshToken = $danaToken->refresh_token;
        if (is_null($refreshToken)) {
            Log::channel('danaTransfer')->debug('in CheckDanaTokenCommand: Dana refresh token is null in dana_tokens table');
            return false;
        }
        $option = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'useReplaceToNavigate' => 'true',
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
                'client_id' => 'Aala-cdhj2sdfHJKsf2d4yy6dsf546Hfg64s',
            ],
        ];
        try {
            $client = new Client();
            $response = $client->request(
                'POST',
                'https://id.danaapp.ir/connect/token',
                $option
            );
        } catch (Exception $exception) {
            Log::channel('danaTransfer')->debug('Error on sending request to Dana for getting new token in CheckDanaTokenCommand');
            Log::channel('danaTransfer')->debug("{$exception->getCode()} - {$exception->getMessage()}");
            return false;
        }

        $result = json_decode($response->getBody(), true);
        $expires_at = Carbon::now('Asia/Tehran')->addSeconds($result['expires_in']);
        $danaToken->update(
            [
                'refresh_token' => $result['refresh_token'],
                'access_token' => $result['access_token'],
                'expires_at' => $expires_at,
                'updated_at' => $now,
            ]
        );
    }
}
