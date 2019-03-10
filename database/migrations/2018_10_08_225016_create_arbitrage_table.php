<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateArbitrageTable
 */
class CreateArbitrageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('arbitrage')->create('arbitrage', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('created_at')->useCurrent();
            $table->string('triplet', 16)->index();
            $table->unsignedTinyInteger('stock_id')->index();
            $table->decimal('profit', 5, 2);
            $table->decimal('profit_quote', 15, 2);
            $table->decimal('bet', 15, 2);
            $table->boolean('notify')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('arbitrage')->dropIfExists('arbitrage');
    }
}
