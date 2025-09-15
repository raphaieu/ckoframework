<?php

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

/**
 * Migration para criar tabelas financeiras
 */
class CreateFinancialTables
{
    public function up()
    {
        // Tabela de categorias
        DB::schema()->create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color', 7)->default('#3B82F6');
            $table->string('type')->default('expense'); // income, expense
            $table->timestamps();
        });

        // Tabela de transações de fluxo de caixa
        DB::schema()->create('cashflow_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->string('type'); // income, expense
            $table->foreignId('category_id')->constrained('categories');
            $table->dateTime('occurred_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Tabela de trades
        DB::schema()->create('trades', function (Blueprint $table) {
            $table->id();
            $table->string('asset');
            $table->string('type'); // day_trade, forex, crypto
            $table->string('side'); // buy, sell
            $table->decimal('quantity', 10, 4);
            $table->decimal('entry_price', 10, 4);
            $table->decimal('exit_price', 10, 4)->nullable();
            $table->decimal('pnl', 10, 2)->default(0);
            $table->decimal('fees', 10, 2)->default(0);
            $table->dateTime('executed_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Tabela de holdings (posições)
        DB::schema()->create('holdings', function (Blueprint $table) {
            $table->id();
            $table->string('asset');
            $table->string('type'); // swing, crypto
            $table->decimal('quantity', 10, 4);
            $table->decimal('entry_price', 10, 4);
            $table->decimal('current_price', 10, 4);
            $table->decimal('total_cost', 10, 2);
            $table->decimal('current_value', 10, 2);
            $table->decimal('pnl', 10, 2);
            $table->decimal('pnl_percent', 5, 2);
            $table->dateTime('bought_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Tabela de usuários (se não existir)
        if (!DB::schema()->hasTable('users')) {
            DB::schema()->create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        DB::schema()->dropIfExists('holdings');
        DB::schema()->dropIfExists('trades');
        DB::schema()->dropIfExists('cashflow_transactions');
        DB::schema()->dropIfExists('categories');
    }
}
