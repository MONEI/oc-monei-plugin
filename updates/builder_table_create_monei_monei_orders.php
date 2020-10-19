<?php namespace MONEI\MONEI\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMoneiMoneiOrders extends Migration
{
    public function up()
    {
        Schema::create('monei_monei_orders', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('transaction_id')->nullable();
            $table->string('account_id')->nullable();
            $table->string('order_id_full')->nullable();
            $table->string('currency')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->integer('total')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_status_msg')->nullable();
            $table->text('description')->nullable();
            $table->text('refunds')->nullable();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('monei_monei_orders');
    }
}