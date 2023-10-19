<?php

namespace Database\Seeders\Tickets;

use App\Models\TicketAction;
use App\Models\TicketActionLog;
use App\Ticket;
use App\TicketAction;
use App\TicketActionLog;
use App\TicketDepartment;
use App\TicketMessage;
use App\User;
use Illuminate\Database\Seeder;
use Schema;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Schema::disableForeignKeyConstraints();
        Ticket::factory()->state(['user_id'=>2])
            ->has(
                TicketActionLog::factory()->state([
                    'user_id'=>2,
                    'action_id'=>TicketAction::CREATE_TICKET
                ]),
                'logs'
            )
            ->has(
                TicketMessage::factory()->state(['user_id'=>2]),
                'messages'
            )
            ->has(
                TicketActionLog::factory()->state([
                    'user_id'=>2,
                    'action_id'=>TicketAction::CREATE_TICKET_MESSAGE,
                    'ticket_message_id'=>1,
                ]),
                'logs'
            )
            ->has(
                TicketMessage::factory()->state(['user_id'=>1]),
                'messages'
            )
            ->has(
                TicketActionLog::factory()->state([
                    'user_id'=>1,
                    'action_id'=>TicketAction::CREATE_TICKET_MESSAGE,
                    'ticket_message_id'=>2,
                ]),
                'logs'
            )
            ->has(
                TicketActionLog::factory()->state([
                    'user_id'=>1,
                    'action_id'=>TicketAction::CHANGE_STATUS_OF_TICKET,
                    'before'=>'پاسخ داده نشده',
                    'after'=>'پاسخ داده شده'
                ]),
                'logs'
            )
            ->count(10)->create();
        Schema::enableForeignKeyConstraints();
    }
}
