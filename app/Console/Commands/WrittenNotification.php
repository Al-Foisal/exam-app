<?php

namespace App\Console\Commands;

use App\Models\Exam;
use App\Models\Notification;
use App\Models\PreliminaryAnswer;
use App\Models\SendExamNotification;
use App\Models\User;
use App\Models\Written;
use App\Models\WrittenAnswer;
use App\Services\FCMService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class WrittenNotification extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'written:written-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle() {
        $written_user = WrittenAnswer::whereHas('written', function ($q) {
            return $q->where('expired_at', '<', Carbon::now('Asia/Dhaka')->toDateTimeString());
        })->pluck('user_id')->toArray();
        $written_group = WrittenAnswer::whereHas('written', function ($q) {
            return $q->where('expired_at', '<', Carbon::now('Asia/Dhaka')->toDateTimeString());
        })->select('written_id')->groupBy('written_id')->get();

        $retain_user = [];

        foreach ($written_group as $w_item) {
            $get_notify_user = SendExamNotification::where('written_id', $w_item->written_id)->whereIn('user_id', $written_user)->pluck('user_id')->toArray();

            $retain_user = array_diff($written_user, $get_notify_user);

            if (count($retain_user) > 0) {

                foreach ($retain_user as $w_r_u) {
                    $user = User::find($w_r_u);

                    if (isset($user->fcm_token)) {

                        $exam_details = Written::where('id', $w_item->written_id)->first();
                        $catt         = '';

                        if ($exam_details->childcategory) {

                            if ($exam_details->childcategory == 'Primary') {
                                $catt = 'প্রাইমারি';
                            } elseif ($exam_details->childcategory == '11 to 20 Grade') {
                                $catt = 'শিক্ষক এবং প্রভাষক';
                            } elseif ($exam_details->childcategory == 'Non-Cadre') {
                                $catt = 'নন-ক্যাডার';
                            } elseif ($exam_details->childcategory == 'Job Solution') {
                                $catt = 'জব সলুশন';
                            } elseif ($exam_details->childcategory == 'Weekly') {
                                $catt = 'সাপ্তাহিক';
                            } elseif ($exam_details->childcategory == 'Daily') {
                                $catt = 'দৈনিক';
                            }

                        } else {

                            if ($exam_details->category == 'BCS') {
                                $catt = 'বিসিএস';
                            } else {
                                $catt = 'ব্যাংক';
                            }

                        }

                        FCMService::send(
                            $user->fcm_token,
                            [
                                'title' => "লাইভ পরীক্ষা",
                                'body'  => $catt . " লিখিত পরীক্ষার খাতা মূল্যায়ন করা হয়েছে, ফলাফল দেখুন।",
                            ]
                        );

                        Notification::create([
                            'name'       => 'লাইভ পরীক্ষা',
                            'details'    => $catt . " লিখিত পরীক্ষার খাতা মূল্যায়ন করা হয়েছে, ফলাফল দেখুন।",
                            'user_id'    => $user->id,
                            'written_id' => $w_item->written_id,
                            'to'         => 'user',
                        ]);

                        SendExamNotification::create([
                            'user_id'    => $w_r_u,
                            'written_id' => $w_item->written_id,
                        ]);
                    }

                }

            }

        }

        //preli notification
        $preli_user = PreliminaryAnswer::whereHas('exam', function ($q) {
            return $q->where('expired_at', '<', Carbon::now('Asia/Dhaka')->toDateTimeString());
        })->pluck('user_id')->toArray();
        $preli_group = PreliminaryAnswer::whereHas('exam', function ($q) {
            return $q->where('expired_at', '<', Carbon::now('Asia/Dhaka')->toDateTimeString());
        })->select('exam_id')->groupBy('exam_id')->get();

        $preli_retain_user = [];

        foreach ($preli_group as $w_item) {
            $get_notify_user = SendExamNotification::where('exam_id', $w_item->exam_id)->whereIn('user_id', $preli_user)->pluck('user_id')->toArray();

            $preli_retain_user = array_diff($preli_user, $get_notify_user);

            if (count($preli_retain_user) > 0) {

                foreach ($preli_retain_user as $w_r_u) {
                    $user = User::find($w_r_u);

                    if (isset($user->fcm_token)) {
                        $exam_details = Exam::where('id', $w_item->exam_id)->first();
                        $catt         = '';

                        if ($exam_details->childcategory) {

                            if ($exam_details->childcategory == 'Primary') {
                                $catt = 'প্রাইমারি';
                            } elseif ($exam_details->childcategory == '11 to 20 Grade') {
                                $catt = 'শিক্ষক এবং প্রভাষক';
                            } elseif ($exam_details->childcategory == 'Non-Cadre') {
                                $catt = 'নন-ক্যাডার';
                            } elseif ($exam_details->childcategory == 'Job Solution') {
                                $catt = 'জব সলুশন';
                            } elseif ($exam_details->childcategory == 'Weekly') {
                                $catt = 'সাপ্তাহিক';
                            } elseif ($exam_details->childcategory == 'Daily') {
                                $catt = 'দৈনিক';
                            }

                        } else {

                            if ($exam_details->category == 'BCS') {
                                $catt = 'বিসিএস';
                            } else {
                                $catt = 'ব্যাংক';
                            }

                        }

                        FCMService::send(
                            $user->fcm_token,
                            [
                                'title' => "লাইভ পরীক্ষা",
                                'body'  => $catt . " প্রিলিমিনারি লাইভ পরীক্ষা শেষ হয়েছে, ফলাফল দেখুন।",
                            ]
                        );

                        Notification::create([
                            'name'       => 'লাইভ পরীক্ষা',
                            'details'    => $catt . " প্রিলিমিনারি লাইভ পরীক্ষা শেষ হয়েছে, ফলাফল দেখুন।",
                            'user_id'    => $user->id,
                            'written_id' => $w_item->exam_id,
                            'to'         => 'user',
                        ]);

                        SendExamNotification::create([
                            'user_id' => $w_r_u,
                            'exam_id' => $w_item->exam_id,
                        ]);
                    }

                }

            }

        }

        $this->info('ok');
    }

}
