<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAmountRefundedToInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal( 'amount_refunded' , 24 , 8 )->default(0); 
            $table->decimal( 'pi_amount_refunded' , 24 , 8 )->default(0);             
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('amount_refunded');     
            $table->dropColumn('pi_amount_refunded');                                             
        });
    }
}
