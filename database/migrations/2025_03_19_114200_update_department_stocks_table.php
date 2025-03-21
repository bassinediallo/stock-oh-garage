<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('department_stocks', function (Blueprint $table) {
            // On s'assure d'avoir les bonnes clés étrangères
            $table->dropForeign(['product_id']);
            $table->dropForeign(['department_id']);
            
            // On recrée les clés étrangères avec les bonnes contraintes
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
                
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('department_stocks', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['department_id']);
            
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('department_id')->references('id')->on('departments');
        });
    }
};
