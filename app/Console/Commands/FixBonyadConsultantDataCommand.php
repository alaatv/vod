<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class FixBonyadConsultantDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaatv:bonyad:consultant:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fix consultant numbers in bonyad';

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
        if (!$this->confirm('continue?')) {
            return false;
        }

        $managerUser = User::whereHas('roles', function ($query) {
            return $query->where('id', 113);
        })->with('consultant')->first();

        //start moshaver laravel
        $moshavers = User::whereHas('roles', function ($query) {
            return $query->where('id', 124);
        })->whereHas('parents', function ($query) use ($managerUser) {
            return $query->where('id', $managerUser->id);
        })->get();
        foreach ($moshavers as $moshaver) {
            $students =
                User::whereHas('parents', function ($query) use ($managerUser) {
                    return $query->where('id', $managerUser->id);
                })
                    ->where('inserted_by', $moshaver->id)
                    ->whereHas('roles', function ($query) {
                        return $query->where('id', 123);
                    })
                    ->count();
            if ($moshaver->consultant->student_register_limit < $students) {
                $moshaver->consultant()->update([
                    'student_register_limit' => $students, 'student_register_number' => $students
                ]);
            } else {
                $moshaver->consultant()->update(['student_register_number' => $students]);
            }
        }
        //end moshaver level


        //start subnetwork level
        $subnetworks = User::whereHas('parents', function ($query) use ($managerUser) {
            return $query->where('id', $managerUser->id);
        })
            ->whereHas('roles', function ($query) {
                return $query->where('id', 131);
            })->get();
        foreach ($subnetworks as $subnetwork) {
            $subMoshavers =
                User::whereHas('parents', function ($query) use ($managerUser) {
                    return $query->where('id', $managerUser->id);
                })
                    ->where('inserted_by', $subnetwork->id)
                    ->whereHas('roles', function ($query) {
                        return $query->where('id', 124);
                    })
                    ->with('consultant')
                    ->get()
                    ->toArray();
            $subUsage =
                array_sum(array_column(array_column($subMoshavers, 'consultant'), 'student_register_limit'));
            $subUsage += User::whereHas('parents', function ($query) use ($managerUser) {
                return $query->where('id', $managerUser->id);
            })
                ->where('inserted_by', $subnetwork->id)
                ->whereHas('roles', function ($query) {
                    return $query->where('id', 123);
                })
                ->count();

            if ($subnetwork->consultant->student_register_limit < $subUsage) {
                $subnetwork->consultant()->update([
                    'student_register_limit' => $subUsage, 'student_register_number' => $subUsage
                ]);
            } else {
                $subnetwork->consultant()->update(['student_register_number' => $subUsage]);
            }
        }
        //end subnetwork level


        //start network level
        $networks = User::whereHas('parents', function ($query) use ($managerUser) {
            return $query->where('id', $managerUser->id);
        })
            ->whereHas('roles', function ($query) {
                return $query->where('id', 130);
            })
            ->get();
        foreach ($networks as $network) {
            $netSubnetworks =
                User::whereHas('parents', function ($query) use ($managerUser) {
                    return $query->where('id', $managerUser->id);
                })
                    ->where('inserted_by', $network->id)
                    ->whereHas('roles', function ($query) {
                        return $query->where('id', 131);
                    })
                    ->with('consultant')
                    ->get()
                    ->toArray();
            $netUsage =
                array_sum(array_column(array_column($netSubnetworks, 'consultant'), 'student_register_limit'));

            $netMoshavers =
                User::whereHas('parents', function ($query) use ($managerUser) {
                    return $query->where('id', $managerUser->id);
                })
                    ->where('inserted_by', $network->id)
                    ->whereHas('roles', function ($query) {
                        return $query->where('id', 124);
                    })
                    ->with('consultant')
                    ->get()
                    ->toArray();
            $netUsage += array_sum(array_column(array_column($netMoshavers, 'consultant'), 'student_register_limit'));

            $netUsage += User::whereHas('parents', function ($query) use ($managerUser) {
                return $query->where('id', $managerUser->id);
            })
                ->where('inserted_by', $network->id)
                ->whereHas('roles', function ($query) {
                    return $query->where('id', 123);
                })
                ->count();

            if ($network->consultant->student_register_limit < $netUsage) {
                $network->consultant()->update([
                    'student_register_limit' => $netUsage, 'student_register_number' => $netUsage
                ]);
            } else {
                $network->consultant()->update(['student_register_number' => $netUsage]);
            }
        }
        //end network level

        //start Bonyad Level
        $manNetworks =
            User::whereHas('parents', function ($query) use ($managerUser) {
                return $query->where('id', $managerUser->id);
            })
                ->where('inserted_by', $managerUser->id)
                ->whereHas('roles', function ($query) {
                    return $query->where('id', 130);
                })
                ->with('consultant')
                ->get()
                ->toArray();
        $manUsage = array_sum(array_column(array_column($manNetworks, 'consultant'), 'student_register_limit'));

        $manSubnetworks =
            User::whereHas('parents', function ($query) use ($managerUser) {
                return $query->where('id', $managerUser->id);
            })
                ->where('inserted_by', $managerUser->id)
                ->whereHas('roles', function ($query) {
                    return $query->where('id', 131);
                })
                ->with('consultant')
                ->get()
                ->toArray();
        $manUsage += array_sum(array_column(array_column($manSubnetworks, 'consultant'), 'student_register_limit'));

        $manMoshavers =
            User::whereHas('parents', function ($query) use ($managerUser) {
                return $query->where('id', $managerUser->id);
            })
                ->where('inserted_by', $managerUser->id)
                ->whereHas('roles', function ($query) {
                    return $query->where('id', 124);
                })
                ->with('consultant')
                ->get()
                ->toArray();
        $manUsage += array_sum(array_column(array_column($manMoshavers, 'consultant'), 'student_register_limit'));

        $manUsage += User::whereHas('parents', function ($query) use ($managerUser) {
            return $query->where('id', $managerUser->id);
        })
            ->where('inserted_by', $managerUser->id)
            ->whereHas('roles', function ($query) {
                return $query->where('id', 123);
            })
            ->count();

        if ($managerUser->consultant->student_register_limit < $manUsage) {
            $managerUser->consultant()->update([
                'student_register_limit' => $manUsage, 'student_register_number' => $manUsage
            ]);
        } else {
            $managerUser->consultant()->update(['student_register_number' => $manUsage]);
        }

        $this->info('done');
        return 0;

    }
}
