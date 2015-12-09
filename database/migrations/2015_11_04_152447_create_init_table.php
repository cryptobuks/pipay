<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 결제 상품 
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string( 'token' , 40 );             
            $table->string('item_desc');
            $table->string('order_id')->nullable();
            $table->decimal( 'amount' , 24 , 8 )->default(0); 
            $table->char( 'currency' , 5 ); 
            $table->smallinteger( 'usage' )->default(1); 
            $table->integer( 'settlement_currency' );                         
            $table->string('email')->nullable();
            $table->string('redirect')->nullable();
            $table->string('callback')->nullable();
            $table->string('ipn')->nullable(); 
            $table->boolean('customer_email')->default(1);
            $table->boolean('customer_name')->default(0);
            $table->boolean('customer_phone')->default(0);
            $table->boolean('customer_address')->default(0); 
            $table->boolean('customer_custom')->default(0);             
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->index('user_id');            
        });

        // 파이 결제 토큰  
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string( 'token' , 40 );            
            $table->string( 'status' , 20 )->default('new');
            $table->string( 'exception_status' , 24 )->nullable(); 
            $table->integer('user_id')->unsigned();
            $table->string( 'api_key' , 40 ); 
            $table->integer('product_id')->nullable();             
            $table->string( 'product_token' , 40 )->nullable();                         
            $table->decimal( 'amount' , 24 , 8 )->default(0);             
            $table->decimal( 'amount_received' , 24 , 8 )->default(0); 
            $table->decimal( 'pi_amount' , 24 , 8 )->default(0); 
            $table->decimal( 'pi_amount_received' , 24 , 8 )->default(0);
            $table->decimal( 'rate' , 24 , 8 )->default(0);            
            $table->char( 'currency' , 5 ); 
            $table->string( 'inbound_address' )->nullable();
            $table->string( 'refund_address' )->nullable();            
            $table->boolean('livemode')->default(1);
            $table->string('item_desc')->nullable();                        
            $table->string('order_id')->nullable();             
            $table->string('reference')->nullable();    
            $table->string('email')->nullable();            
            $table->timestamp('expiration_at');
            $table->string( 'url' );              
            $table->string( 'payment_url' );
            $table->string('customer_email')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_address')->nullable(); 
            $table->string('customer_custom')->nullable();             
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->unique('token');
            $table->index('user_id');         
        });

        // 파이 결제 
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string( 'token' , 40 ); 
            $table->integer('user_id')->unsigned();
            $table->string( 'api_key' , 40 );            
            $table->integer('buyer_id')->unsigned()->nullable();                        
            $table->integer('invoice_id')->unsigned();            
            $table->string( 'invoice_token' , 40 );             
            $table->decimal( 'amount' , 24 , 8 )->default(0);     
            $table->decimal( 'amount_refunded' , 24 , 8 )->default(0); 
            $table->integer('refund_id')->unsigned()->nullable();            
            $table->decimal( 'fee' , 24 , 8 )->default(0);       
            $table->integer('fee_id')->unsigned();             
            $table->char( 'currency' , 5 ); 
            $table->boolean('livemode')->default(1);
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->unique('token');            
            $table->index('user_id');                        
            $table->index('invoice_id');                                    
        });

        // 결제 트랜젝션 로그 
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->decimal( 'amount' , 24 , 8 )->default(0);             
            $table->char( 'currency' , 5 ); 
            $table->decimal( 'fee' , 24 , 8 )->default(0);                                     
            $table->integer('fee_id')->unsigned();                        
            $table->decimal( 'net' , 24 , 8 )->default(0);                         
            $table->integer( 'source_id' )->nullable();            
            $table->string( 'source_type' , 30 )->nullable(); 
            $table->string( 'status' , 20 )->default('available'); 
            $table->string( 'type' , 30 )->nullable();             
            $table->string( 'url' )->nullable();                         
            $table->string( 'description' )->nullable();                                     
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->index('user_id');                        
            $table->index( ['source_id' , 'source_type'] );                                    
        });

        // 파이 환불 
        Schema::create('refunds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('payment_id')->unsigned();             
            $table->decimal( 'amount' , 24 , 8 )->default(0);         
            $table->decimal( 'pi_amount' , 24 , 8 )->default(0);                             
            $table->char( 'currency' , 5 ); 
            $table->string('reason', 30)->nullable();                        
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->index('user_id');                        
        });

        // 파이 수수료 
        Schema::create('fees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('payment_id')->unsigned();             
            $table->decimal( 'amount' , 24 , 8 )->default(0);         
            $table->char( 'currency' , 5 ); 
            $table->string( 'description' )->nullable();   
            $table->string( 'type' , 30 )->nullable();                                                           
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->index('user_id');                        
        });

        // 정산 후 송금  
        Schema::create('transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->string( 'token' , 40 );             
            $table->integer('user_id')->unsigned();
            $table->string( 'status' , 20 )->default('pending');            
            $table->string( 'type' , 20 )->default('pipay_account');                        
            $table->decimal( 'amount' , 24 , 8 )->default(0);         
            $table->decimal( 'amount_reversed' , 24 , 8 )->default(0);                             
            $table->char( 'currency' , 5 ); 
            $table->string( 'description' )->nullable(); 
            $table->string( 'destination_type' , 30 )->nullable(); 
            $table->integer( 'destination_id' )->nullable(); 
            $table->boolean('livemode')->default(1);            
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->unique('token');  
            $table->index('user_id'); 
        });


        // 파이 받기 트랜젝션 테이블  
        Schema::create('pi_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->char( 'currency' , 5 ); 
            $table->string( 'txid' );            
            $table->decimal( 'amount' , 24 , 8 )->default(0);     
            $table->integer('confirmations')->default(0);                
            $table->string( 'address' )->nullable(); 
            $table->integer( 'txout' )->default(0); 
            $table->smallInteger( 'state' )->default(100); 
            $table->timestamp('received_at')->nullable();            
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->index(  ['txid' , 'txout'] );                        
        });

        // 파이 결제 계정 
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();            
            $table->char( 'currency' , 5 ); 
            $table->decimal( 'balance' , 24 , 8 )->default(0);     
            $table->decimal( 'locked' , 24 , 8 )->default(0);                 
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->index('user_id');                        
        });

        // 파이 결제 계정  장부 
        Schema::create('legders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();            
            $table->integer('account_id')->unsigned();                        
            $table->char( 'currency' , 5 ); 
            $table->smallInteger('fun'); 
            $table->smallInteger('reason');
            $table->decimal( 'balance' , 24 , 8 )->default(0);     
            $table->decimal( 'locked' , 24 , 8 )->default(0);                 
            $table->decimal( 'fee' , 24 , 8 )->default(0);                             
            $table->decimal( 'amount' , 24 , 8 )->default(0);                                         
            $table->integer( 'modifiable_id' )->nullable();                                                     
            $table->string( 'modifiable_type' , 40 )->nullable();                                                                 
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->index( [ 'user_id' , 'reason' ] );
            $table->index( [ 'account_id' , 'reason' ] );
            $table->index( [ 'modifiable_id' , 'modifiable_type'] );

        });

        // 유저 API key
        Schema::create('users_key', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->primary();            
            $table->string('live_api_key', 40);
            $table->string('test_api_key', 40);            
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->unique( 'live_api_key' );
            $table->unique( 'test_api_key' );            

        });

        Schema::create('users_profile', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->primary();            
            $table->string('email');
            $table->timestamp('last_login')->nullable();
            $table->string('username')->nullable();
            $table->string('cellphone' , 32 )->nullable();            
            $table->smallInteger('level' )->nullable();
            $table->integer('category' )->nullable();            
            $table->tinyInteger('shop_type' )->nullable(); 
            $table->string('company' )->nullable();   
            $table->string('website' )->nullable();             
            $table->string('phone' )->nullable();     
            $table->string('logo' )->nullable();                 
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->unique('email');
        });


        Schema::create('two_factors', function($table)
        {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('opt_secret')->nullable();
            $table->boolean('activated')->nullable();
            $table->string('type' , 30 )->nullable();
            $table->timestamp('last_verify_at')->nullable();
            $table->timestamp('refreshed_at')->nullable();

            $table->engine = 'InnoDB';
            $table->index('user_id');
        });


        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop('products');        
        Schema::drop('invoices'); 
        Schema::drop('payments'); 
        Schema::drop('refunds'); 
        Schema::drop('fees');     
        Schema::drop('transfers');         
        Schema::drop('transactions'); 
        Schema::drop('pi_transactions');         
        Schema::drop('accounts');         
        Schema::drop('ledgers'); 
        Schema::drop('users_key');
        Schema::drop('users_profile');        
        Schema::drop('two_factors');        

    }
}
