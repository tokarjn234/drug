<?php


namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Photo;
use App\Models\Message;
use Hash;

class UserCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usercmd {action} {id?}';

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
     * @return mixed
     */
    public function handle()
    {
        $action = $this->argument('action');

        $this->{$action}();

    }

    public function test() {
        $u = Message::first();
        pr($u->toArray());


    }

    public function encrypt_msg() {
        Message::chunk(200, function($messages) {
            foreach ($messages as $msg) {
                $msgArr = $msg->toArray();

                $msg->title = $msgArr['title'];
                $msg->content = $msgArr['content'];
                $msg->save();
                $this->info('Processed : ' . $msg->id);
            }
        });
    }

    public function encrypt() {
        User::chunk(200, function($users)
        {
            foreach ($users as $user)
            {
                $userArr = $user->toArray();

                $user->email = $userArr['email'];
                $user->first_name = $userArr['first_name'];
                $user->last_name = $userArr['last_name'];
                $user->first_name_kana = $userArr['first_name_kana'];
                $user->last_name_kana = $userArr['last_name_kana'];
                $user->phone_number = $userArr['phone_number'];
                $user->address = $userArr['address'];

                $user->save();
                $this->line('Processed: ' .$userArr['id'] .':'. $userArr['email'] .',' . $userArr['first_name'] . ' ' . $userArr['last_name']);
            }
        });
    }

    public function view() {
        $id = $this->argument('id');

        if (empty ($id)) {
            $id = $this->ask('Enter UserID:');
        }

        print_r(User::find($id)->toArray());

    }

    public function search() {
        $keyword = $this->argument('id');
        if (empty ($keyword)) {$keyword = $this->ask('Enter keyword to search:'); }

        $users = User::searchEncrypted('full_name_index', $keyword)->get();

        if (!empty ($users)) {
            foreach ($users as $user) {
                $this->line($user->id . ',' . $user->email . ',' . $user->first_name . ' ' . $user->last_name);
            }
        }

        $users = User::searchEncrypted('full_name_kana_index', $keyword)->get();

        if (!empty ($users)) {
            foreach ($users as $user) {
                $this->line($user->id . ',' . $user->email . ',' . $user->first_name . ' ' . $user->last_name);
            }
        }


    }

    public function edit() {
        $id = $this->argument('id');

        if (empty ($id)) {
            $id = $this->ask('Enter UserID:');
        }

        $user = User::find($id);
        print_r($user->toArray());
        $field = $this->ask('Enter field that you want to change value:');
        $value = $this->ask('Enter the field value:');

        if ($field === 'password') {
            $value = Hash::make($value);
        }

        $user->$field = $value;

        if ($user->save()) {
            $this->info('Save info successfully');
        } else {
            $this->info('Save info failed');
        }

    }

    public function encrypt_photo() {
        Photo::chunk(200, function($photos)
        {
            foreach ($photos as $photo)
            {
                echo ('Process : ' . $photo->id . '...');
                try {
                    if ($photo->toEncryptImage()) {
                        echo "OK\n";
                    } else {
                        echo "FAILED\n";
                    }
                } catch (\Exception $e) {
                    echo $e->getMessage() . "\n";
                }

            }
        });

    }

}