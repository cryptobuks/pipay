<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Transafer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:Transfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Settlement payment';

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
        // 이전날의 결제 완료된 결제 요구 테이블을 통계를 내서 구한다
        // 루프를 돌면서 아이디마다 정산 처리 
        // 완료
    }
}
