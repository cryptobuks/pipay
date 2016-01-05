<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSettlementCurrencyToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_profile', function (Blueprint $table) {
            $table->char('settlement_currency' , 5 )->default('PI');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_profile', function (Blueprint $table) {
            $table->dropColumn('settlement_currency');                   
        });
    }
}
