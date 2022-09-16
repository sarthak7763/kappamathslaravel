<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnV23 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       if ( Schema::hasTable('pages') ) {
            Schema::table('pages', function (Blueprint $table) {
                if (!Schema::hasColumn('pages', 'show_in_menu')){
                    $table->integer('show_in_menu')->default(0)->after('status');
                }
            });
        }
        if(Schema::hasTable('questions')){
            Schema::table('questions', function (Blueprint $table) {
                if (!Schema::hasColumn('questions', 'e')){
                    $table->string('e', 200)->nullable();
                }
            });
        }
        if(Schema::hasTable('questions')){
            Schema::table('questions', function (Blueprint $table) {
                if (!Schema::hasColumn('questions', 'f')){
                    $table->string('f', 200)->nullable();
                }
            });
        }
        if(Schema::hasTable('questions')){
            Schema::table('questions', function (Blueprint $table) {
                if (!Schema::hasColumn('questions', 'question_audio')){
                    $table->longtext('question_audio')->nullable();
                }
            });
        }
        if(Schema::hasTable('settings')){
            Schema::table('settings', function (Blueprint $table) {
                if (!Schema::hasColumn('settings', 'wel_mail')){
                    $table->booleal('wel_mail')->default(0);
                }
            });
        }
        if(Schema::hasTable('settings')){
            Schema::table('settings', function (Blueprint $table) {
                if (!Schema::hasColumn('settings', 'coming_soon')){
                    $table->booleal('coming_soon')->default(0);
                }
            });
        }
        if(Schema::hasTable('settings')){
            Schema::table('settings', function (Blueprint $table) {
                if (!Schema::hasColumn('settings', 'comingsoon_enabled_ip')){
                    $table->longtext('comingsoon_enabled_ip')->nullable();
                }
            });
        }
        if(Schema::hasTable('users')){
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'image')){
                    $table->string('image',191)->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasTable('pages')){
            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('show_in_menu');
            });
        }
        if(Schema::hasTable('questions')){
            Schema::table('questions', function (Blueprint $table) {
                $table->dropColumn('e');
            });
        }
        if(Schema::hasTable('questions')){
            Schema::table('questions', function (Blueprint $table) {
                $table->dropColumn('f');
            });
        }
        if(Schema::hasTable('questions')){
            Schema::table('questions', function (Blueprint $table) {
                $table->dropColumn('question_audio');
            });
        }
        if(Schema::hasTable('settings')){
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('wel_mail');
            });
        }
        if(Schema::hasTable('settings')){
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('coming_soon');
            });
        }
        if(Schema::hasTable('settings')){
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('comingsoon_enabled_ip');
            });
        }
        if(Schema::hasTable('users')){
            Schema::table('image', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
}
