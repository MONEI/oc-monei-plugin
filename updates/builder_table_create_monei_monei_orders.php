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
            $table->string('order_id_full')->nullable();
            $table->string('monei_order_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->integer('total')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_status_msg')->nullable();
            $table->dateTime('order_date')->nullable();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('monei_monei_orders');
    }
}