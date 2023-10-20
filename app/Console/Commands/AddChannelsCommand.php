<?php

namespace App\Console\Commands;

use App\Models\Block;
use App\Models\BlockType;
use App\Models\Channel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Facades\Excel;

class AddChannelsCommand extends Command
{

    public $channelBlockType;
    public $file = null;
    public $sheets = [];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:add:channels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $this->setBlockChannelType()->loadFile()->setSheets()->createChannels();

        return 0;
    }

    public function createChannels()
    {

        if (!$this->confirm("Do you pout you file in 'storage/app/public/channels' path?")) {
            return 0;
        }
        if (!$this->confirm(count($this->sheets).' Sheets exists in excel file, do you want to continue?')) {
            return 0;
        }

        $progressbar = $this->output->createProgressBar(count($this->sheets));

        $progressbar->start();

        foreach ($this->sheets as $sheet) {
            $this->info("\ncreate channel ".$this->readChannelName($sheet)."\n");

            $this->createChannelAndBlock($sheet);

            $progressbar->advance();
        }

        $progressbar->finish();
    }

    public function readChannelName($sheet)
    {
        return $sheet[0][0];
    }

    public function createChannelAndBlock($sheet)
    {
        foreach ($sheet[0] as $index => $title) {
            if (is_null($title) || $index == 0) {
                continue;
            }
            $this->info("\n \tcreate block $title \n");

            $channel = $this->createChannel($title);
            $block = $this->createBlock($title);
            $this->attachBlockToChannel($channel, $block);

            $this->createBlockables($block, $index, $sheet);
        }

    }

    public function createChannel($title)
    {
        return Channel::create(['title' => $title]);
    }

    public function createBlock($blockTitle): Block
    {
        return Block::create([
            'title' => $blockTitle,
            'type' => $this->channelBlockType // todo : add new type for channels
        ]);
    }

    protected function attachBlockToChannel(Channel $channel, Block $block): void
    {
        $channel->blocks()->sync($block);
    }

    public function createBlockables(Block $block, $blockIndex, $sheet)
    {
        $setIds = [];
        foreach ($sheet as $rowIndex => $data) {
            if ($rowIndex > 1 && $setId = $data[$blockIndex]) {

                $setIds[] = $setId;
            }
        }

        $block->sets()->attach($setIds);
    }

    public function setSheets()
    {
        $data = Excel::toArray(new ChannelImpoer(), $this->file);
        foreach ($data as $sheet => $values) {
            $this->sheets[$sheet] = $values;
        }

        return $this;
    }

    public function loadFile()
    {
        $this->file = File::files(storage_path().'/app/public/channels')[0];
        return $this;
    }

    protected function setBlockChannelType()
    {
        if (!$channelBlockType = BlockType::where('name', 'channel')->first()) {
            $channelBlockType = BlockType::create([
                'name' => 'channel',
                'display_name' => 'کانال',
            ]);
            $this->info("\nBlock type of channel added.\n");
        }
        $this->channelBlockType = $channelBlockType?->id;

        return $this;
    }
}

class ChannelImpoer implements ToArray
{
    public function array(array $array)
    {
        // TODO: Implement array() method.
    }
}
